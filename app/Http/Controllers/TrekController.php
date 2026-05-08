<?php

namespace App\Http\Controllers;

use App\Models\Trek;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrekController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $user = $this->authenticate($request);

        if ($user->isAdmin()) {
            return response()->json(Trek::all());
        }

        return response()->json($user->treks()->get());
    }

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

    public function show(Request $request, Trek $trek): JsonResponse
    {
        $user = $this->authenticate($request);

        if (! $user->isAdmin()) {
            $this->authorizeOwner($user, $trek);
        }

        return response()->json($trek);
    }

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
