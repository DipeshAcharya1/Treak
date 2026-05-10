<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends ApiController
{
    public function index($trekId)
    {
        $reviews = \App\Models\Review::where('trek_id', $trekId)->with('user:id,name')->get();
        return response()->json($reviews);
    }

    public function store(Request $request)
    {
        $user = $this->authenticate($request);
        $validated = $request->validate([
            'trek_id' => 'required|exists:treks,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = $user->reviews()->create($validated);
        return response()->json($review, 201);
    public function listAll(Request $request)
    {
        $user = $this->authenticate($request);
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reviews = \App\Models\Review::with(['user:id,name', 'trek:id,title'])->get();
        return response()->json($reviews);
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->authenticate($request);
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review = \App\Models\Review::findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'Review deleted successfully']);
    }
}
