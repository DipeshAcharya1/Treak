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
        return $this->successResponse($bookings);
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
        
        // Internal Capacity Check (Simplified for cleanup)
        if ($trek->vehicles->count() > 0) {
            $totalCapacity = $trek->vehicles->sum('capacity');
            $currentBooked = \App\Models\Booking::where('trek_id', $trek->id)
                ->where('booking_date', $validated['booking_date'])
                ->where('status', '!=', 'cancelled')
                ->sum('number_of_people');
                
            if (($currentBooked + $validated['number_of_people']) > $totalCapacity) {
                return $this->errorResponse("Capacity full for this date. Only " . ($totalCapacity - $currentBooked) . " seats left.", 422);
            }
        }

        $booking = $user->bookings()->create($validated);
        return $this->successResponse($booking, 'Booking created successfully', 201);
    }

    public function show(Request $request, $id)
    {
        $user = $this->authenticate($request);
        $booking = $user->bookings()->with('trek')->findOrFail($id);
        return $this->successResponse($booking);
    }

    public function cancel(Request $request, $id)
    {
        $user = $this->authenticate($request);
        $booking = $user->bookings()->with('trek')->findOrFail($id);
        $booking->update(['status' => 'cancelled']);
        return $this->successResponse($booking, 'Booking cancelled successfully');
    }

    public function listAll(Request $request)
    {
        $user = $this->authenticate($request);
        if (!$user->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $bookings = \App\Models\Booking::with(['user:id,name,email', 'trek:id,title'])->get();
        return $this->successResponse($bookings);
    }

    public function updateStatus(Request $request, $id)
    {
        $user = $this->authenticate($request);
        if (!$user->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled'
        ]);

        $booking = \App\Models\Booking::with(['user', 'trek'])->findOrFail($id);
        $booking->update(['status' => $validated['status']]);

        return $this->successResponse($booking, 'Booking status updated');
    }

    public function processPayment(Request $request, $id)
    {
        $user = $this->authenticate($request);
        $booking = $user->bookings()->findOrFail($id);

        $validated = $request->validate([
            'payment_method' => 'required|string',
            'transaction_id' => 'required|string',
        ]);

        $booking->update([
            'payment_status' => 'paid',
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $validated['transaction_id'],
            'status' => 'confirmed',
        ]);

        return $this->successResponse($booking, 'Payment processed successfully');
    }
}

