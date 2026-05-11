<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItineraryController extends ApiController
{
    public function index($trekId)
    {
        $itineraries = \App\Models\Itinerary::where('trek_id', $trekId)->orderBy('day_number')->get();
        return $this->successResponse($itineraries);
    }

    public function store(Request $request, $trekId)
    {
        $user = $this->authenticate($request);
        if (!$user->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validated = $request->validate([
            'day_number' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'accommodation' => 'nullable|string',
            'meals' => 'nullable|string',
        ]);

        $itinerary = \App\Models\Trek::findOrFail($trekId)->itineraries()->create($validated);
        return $this->successResponse($itinerary, 'Itinerary added successfully', 201);
    }
}
