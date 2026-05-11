<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends ApiController
{
    /**
     * Register a new user and return an API token.
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = Str::random(60);
        $user->api_token = hash('sha256', $token);
        $user->save();

        return $this->successResponse([
            'user' => $user->only(['id', 'name', 'email', 'role']),
            'token' => $token,
        ], 'Registration successful', 201);
    }

    /**
     * Authenticate a user and return an API token.
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', '=', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return $this->errorResponse('Invalid credentials.', 401);
        }

        $token = Str::random(60);
        $user->api_token = hash('sha256', $token);
        $user->save();

        return $this->successResponse([
            'user' => $user->only(['id', 'name', 'email', 'role']),
            'token' => $token,
        ], 'Login successful');
    }

    /**
     * Get the currently authenticated user's profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $this->authenticate($request);

        return $this->successResponse(['user' => $user->only(['id', 'name', 'email', 'role'])]);
    }

    /**
     * Invalidate the current user's API token.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $this->authenticate($request);

        $user->api_token = null;
        $user->save();

        return $this->successResponse(null, 'Logged out successfully.');
    }

    /**
     * Update the authenticated user's profile (partial update allowed).
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $this->authenticate($request);

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($data);

        return $this->successResponse([
            'user' => $user->only(['id', 'name', 'email', 'role']),
        ], 'Profile updated successfully.');
    }

    /**
     * List all users (admin only).
     * Admin access is enforced by the 'admin' middleware on the route.
     */
    public function listUsers(Request $request): JsonResponse
    {
        $users = User::all(['id', 'name', 'email', 'role', 'created_at', 'updated_at']);

        return $this->successResponse($users);
    }
    /**
     * Update a user (Admin only).
     */
    public function updateUser(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['sometimes', 'required', 'in:user,admin'],
        ]);

        $user->update($data);

        return $this->successResponse([
            'user' => $user->only(['id', 'name', 'email', 'role', 'created_at', 'updated_at']),
        ], 'User updated successfully.');
    }

    /**
     * Delete a user (Admin only).
     */
    public function deleteUser(User $user): JsonResponse
    {
        // Optionally prevent admin from deleting themselves
        if (request()->user() && request()->user()->id === $user->id) {
            return $this->errorResponse('You cannot delete yourself.', 403);
        }

        $user->delete();

        return $this->successResponse(null, 'User deleted successfully.');
    }
}
