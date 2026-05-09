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
            return response()->json(Trek::with('user:id,name,email')->get());
        }

        return response()->json($user->treks()->get());
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
            'date' => ['nullable', 'date'],
        ]);

        $trek = $user->treks()->create($data);

        return response()->json($trek, 201);
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

        return response()->json($trek);
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
            'date' => ['nullable', 'date'],
        ]);

        $trek->update($data);

        return response()->json($trek);
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

        return response()->json(['message' => 'Trek deleted successfully.']);
    }
}
