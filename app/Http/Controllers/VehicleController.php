<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VehicleController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $vehicles = \App\Models\Vehicle::all();
        return response()->json($vehicles);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $this->authenticate($request);
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'plate_number' => 'required|string|unique:vehicles',
            'driver_name' => 'nullable|string|max:255',
            'driver_contact' => 'nullable|string|max:255',
        ]);

        $vehicle = \App\Models\Vehicle::create($validated);
        return response()->json($vehicle, 201);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $vehicle = \App\Models\Vehicle::with('treks')->findOrFail($id);
        return response()->json($vehicle);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $this->authenticate($request);
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $vehicle = \App\Models\Vehicle::findOrFail($id);
        $vehicle->delete();

        return response()->json(['message' => 'Vehicle deleted successfully']);
    }
}
