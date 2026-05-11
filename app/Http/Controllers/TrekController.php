<?php

namespace App\Http\Controllers;

use App\Models\Trek;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrekController extends ApiController
{
    /**
     * List treks. Admins see all treks; regular users see only their own.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $this->authenticate($request);

        if ($user->isAdmin()) {
            return $this->successResponse(Trek::with(['user:id,name,email', 'guides', 'vehicles'])->get());
        }

        return $this->successResponse($user->treks()->get());
    }

    /**
     * Create a new trek for the authenticated user.
     */
    public function store(Request $request): JsonResponse
    {
        $user = $this->authenticate($request);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric'],
            'location' => ['nullable', 'string'],
            'duration_days' => ['nullable', 'integer'],
            'difficulty' => ['required', 'string', 'in:easy,moderate,difficult'],
            'max_altitude' => ['nullable', 'integer'],
            'image_url' => ['nullable', 'string', 'url'],
            'date' => ['nullable', 'date'],
            'guide_ids' => ['nullable', 'array'],
            'guide_ids.*' => ['exists:guides,id'],
            'vehicle_ids' => ['nullable', 'array'],
            'vehicle_ids.*' => ['exists:vehicles,id'],
        ]);

        $trek = $user->treks()->create(collect($data)->except(['guide_ids', 'vehicle_ids'])->toArray());

        if ($request->has('guide_ids')) {
            $trek->guides()->sync($request->guide_ids);
        }
        if ($request->has('vehicle_ids')) {
            $trek->vehicles()->sync($request->vehicle_ids);
        }

        return $this->successResponse($trek, 'Trek created successfully', 201);
    }

    /**
     * Show a specific trek. Admins can view any; users can only view their own.
     */
    public function show(Request $request, Trek $trek): JsonResponse
    {
        $user = $this->authenticate($request);

        if (! $user->isAdmin()) {
            $this->authorizeOwner($user, $trek);
        }

        return $this->successResponse($trek);
    }

    /**
     * Update a specific trek. Admins can update any; users can only update their own.
     */
    public function update(Request $request, Trek $trek): JsonResponse
    {
        $user = $this->authenticate($request);

        if (! $user->isAdmin()) {
            $this->authorizeOwner($user, $trek);
        }

        $data = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric'],
            'location' => ['nullable', 'string'],
            'duration_days' => ['nullable', 'integer'],
            'difficulty' => ['sometimes', 'required', 'string', 'in:easy,moderate,difficult'],
            'max_altitude' => ['nullable', 'integer'],
            'image_url' => ['nullable', 'string', 'url'],
            'date' => ['nullable', 'date'],
            'guide_ids' => ['nullable', 'array'],
            'guide_ids.*' => ['exists:guides,id'],
            'vehicle_ids' => ['nullable', 'array'],
            'vehicle_ids.*' => ['exists:vehicles,id'],
        ]);

        $trek->update(collect($data)->except(['guide_ids', 'vehicle_ids'])->toArray());

        if ($request->has('guide_ids')) {
            $trek->guides()->sync($request->guide_ids);
        }
        if ($request->has('vehicle_ids')) {
            $trek->vehicles()->sync($request->vehicle_ids);
        }

        return $this->successResponse($trek, 'Trek updated successfully');
    }

    /**
     * Delete a specific trek. Admins can delete any; users can only delete their own.
     */
    public function destroy(Request $request, Trek $trek): JsonResponse
    {
        $user = $this->authenticate($request);

        if (! $user->isAdmin()) {
            $this->authorizeOwner($user, $trek);
        }

        $trek->delete();

        return $this->successResponse(null, 'Trek deleted successfully.');
    }

    /**
     * Public list of all treks for discovery.
     */
    public function listAll(): JsonResponse
    {
        return $this->successResponse(Trek::with(['user:id,name', 'guides', 'vehicles'])->get());
    }

    /**
     * Public details of a specific trek.
     */
    public function showPublic(Trek $trek): JsonResponse
    {
        return $this->successResponse($trek->load(['itineraries', 'reviews.user:id,name', 'guides', 'vehicles']));
    }
}
