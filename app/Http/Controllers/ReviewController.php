<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends ApiController
{
    public function index($trekId)
    {
        $reviews = \App\Models\Review::where('trek_id', $trekId)->with('user:id,name')->get();
        return $this->successResponse($reviews);
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
        return $this->successResponse($review, 'Review submitted successfully', 201);
    }

    public function listAll(Request $request)
    {
        $user = $this->authenticate($request);
        if (!$user->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $reviews = \App\Models\Review::with(['user:id,name', 'trek:id,title'])->get();
        return $this->successResponse($reviews);
    }

    public function destroy(Request $request, $id)
    {
        $user = $this->authenticate($request);
        if (!$user->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $review = \App\Models\Review::findOrFail($id);
        $review->delete();

        return $this->successResponse(null, 'Review deleted successfully');
    }
}
