<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuideController extends Controller
{
    public function index()
    {
        $guides = \App\Models\Guide::all();
        return response()->json($guides);
    }

    public function store(Request $request)
    {
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

    public function show($id)
    {
        $guide = \App\Models\Guide::with('treks')->findOrFail($id);
        return response()->json($guide);
    }
}
