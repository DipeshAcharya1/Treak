<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GuideController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $guides = \App\Models\Guide::all();
        return response()->json($guides);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $this->authenticate($request);
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'experience_years' => 'integer|min:0',
            'bio' => 'nullable|string',
            'contact_number' => 'nullable|string|max:255',
            'profile_image_url' => 'nullable|url',
            'languages_spoken' => 'nullable|string|max:255',
        ]);

        $guide = \App\Models\Guide::create($validated);
        return response()->json($guide, 201);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $guide = \App\Models\Guide::with('treks')->findOrFail($id);
        return response()->json($guide);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $this->authenticate($request);
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $guide = \App\Models\Guide::findOrFail($id);
        $guide->delete();

        return response()->json(['message' => 'Guide deleted successfully']);
    }
}
