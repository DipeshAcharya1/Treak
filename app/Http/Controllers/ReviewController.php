<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index($trekId)
    {
        $reviews = \App\Models\Review::where('trek_id', $trekId)->with('user:id,name')->get();
        return response()->json($reviews);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trek_id' => 'required|exists:treks,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = auth()->user()->reviews()->create($validated);
        return response()->json($review, 201);
    }
}
