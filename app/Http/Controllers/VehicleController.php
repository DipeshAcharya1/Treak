<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = \App\Models\Vehicle::all();
        return response()->json($vehicles);
    }

    public function store(Request $request)
    {
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

    public function show($id)
    {
        $vehicle = \App\Models\Vehicle::with('treks')->findOrFail($id);
        return response()->json($vehicle);
    }
}
