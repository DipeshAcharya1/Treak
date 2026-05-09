<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = auth()->user()->bookings()->with('trek')->get();
        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trek_id' => 'required|exists:treks,id',
            'booking_date' => 'required|date',
            'number_of_people' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
        ]);

        $booking = auth()->user()->bookings()->create($validated);
        return response()->json($booking, 201);
    }

    public function show($id)
    {
        $booking = auth()->user()->bookings()->with('trek')->findOrFail($id);
        return response()->json($booking);
    }

    public function cancel($id)
    {
        $booking = auth()->user()->bookings()->findOrFail($id);
        $booking->update(['status' => 'cancelled']);
        return response()->json(['message' => 'Booking cancelled successfully', 'booking' => $booking]);
    }
}
