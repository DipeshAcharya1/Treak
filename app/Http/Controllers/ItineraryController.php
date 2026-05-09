<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItineraryController extends Controller
{
    public function index($trekId)
    {
        $itineraries = \App\Models\Itinerary::where('trek_id', $trekId)->orderBy('day_number')->get();
        return response()->json($itineraries);
    }

    public function store(Request $request, $trekId)
    {
        $validated = $request->validate([
            'day_number' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $itinerary = \App\Models\Trek::findOrFail($trekId)->itineraries()->create($validated);
        return response()->json($itinerary, 201);
    }
}
