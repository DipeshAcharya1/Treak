<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\BookingConfirmed;
use App\Notifications\BookingCancelled;
use App\Notifications\NewBookingAdminAlert;
use App\Models\User;

class BookingController extends ApiController
{
    public function index(Request $request)
    {
        $user = $this->authenticate($request);
        $bookings = $user->bookings()->with(['trek.guides', 'trek.vehicles'])->get();
        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $user = $this->authenticate($request);
        $validated = $request->validate([
            'trek_id' => 'required|exists:treks,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'number_of_people' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
        ]);

        $trek = \App\Models\Trek::with('vehicles')->findOrFail($validated['trek_id']);
        $duration = $trek->duration_days || 1;
        $startDate = new \DateTime($validated['booking_date']);
        $endDate = clone $startDate;
        $endDate->modify('+' . ($duration - 1) . ' days');
        
        // 1. Check for Vehicle Overlaps across different treks
        if ($trek->vehicles->count() > 0) {
            foreach ($trek->vehicles as $vehicle) {
                // Find other bookings that use this vehicle during the requested period
                $conflictingBookings = \App\Models\Booking::where('status', '!=', 'cancelled')
                    ->where('trek_id', '!=', $trek->id) // Different trek
                    ->with('trek') // Eager load trek for duration
                    ->whereHas('trek.vehicles', function($query) use ($vehicle) {
                        $query->where('vehicles.id', $vehicle->id);
                    })
                    ->get()
                    ->filter(function($b) use ($startDate, $endDate) {
                        $bStart = new \DateTime($b->booking_date);
                        $bDuration = $b->trek->duration_days || 1;
                        $bEnd = clone $bStart;
                        $bEnd->modify('+' . ($bDuration - 1) . ' days');
                        
                        return ($startDate <= $bEnd && $endDate >= $bStart);
                    });

                if ($conflictingBookings->count() > 0) {
                    return response()->json([
                        'message' => "Vehicle {$vehicle->type} ({$vehicle->plate_number}) is already booked for another trek during this period."
                    ], 422);
                }
            }

            // 2. Internal Capacity Check for THIS trek
            $totalCapacity = $trek->vehicles->sum('capacity');
            $currentBooked = \App\Models\Booking::where('trek_id', $trek->id)
                ->where('booking_date', $validated['booking_date'])
                ->where('status', '!=', 'cancelled')
                ->sum('number_of_people');
                
            if (($currentBooked + $validated['number_of_people']) > $totalCapacity) {
                return response()->json([
                    'message' => "Capacity full for this date. Only " . ($totalCapacity - $currentBooked) . " seats left.",
                ], 422);
            }
        }

        $booking = $user->bookings()->create($validated);
        
        // Load trek for notification details
        $booking->load(['trek', 'user']);
        
        // Notify User
        $user->notify(new BookingConfirmed($booking));
        
        // Notify Admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewBookingAdminAlert($booking));
        }

        return response()->json($booking, 201);
    }

    public function show(Request $request, $id)
    {
        $user = $this->authenticate($request);
        $booking = $user->bookings()->with('trek')->findOrFail($id);
        return response()->json($booking);
    }

    public function cancel(Request $request, $id)
    {
        $user = $this->authenticate($request);
        $booking = $user->bookings()->with('trek')->findOrFail($id);
        $booking->update(['status' => 'cancelled']);
        
        // Notify User
        $user->notify(new BookingCancelled($booking));
        
        return response()->json(['message' => 'Booking cancelled successfully', 'booking' => $booking]);
    }

    /**
     * Admin: List all bookings in the system.
     */
    public function listAll(Request $request)
    {
        $user = $this->authenticate($request);
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $bookings = \App\Models\Booking::with(['user:id,name,email', 'trek:id,title'])->get();
        return response()->json($bookings);
    }

    /**
     * Admin: Update status of any booking.
     */
    public function updateStatus(Request $request, $id)
    {
        $user = $this->authenticate($request);
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled'
        ]);

        $booking = \App\Models\Booking::with(['user', 'trek'])->findOrFail($id);
        $oldStatus = $booking->status;
        $booking->update(['status' => $validated['status']]);

        // Notify user if status changed to cancelled or confirmed
        if ($oldStatus !== $validated['status']) {
            if ($validated['status'] === 'confirmed') {
                $booking->user->notify(new BookingConfirmed($booking));
            } elseif ($validated['status'] === 'cancelled') {
                $booking->user->notify(new BookingCancelled($booking));
            }
        }

        return response()->json(['message' => 'Booking status updated', 'booking' => $booking]);
    }
}
