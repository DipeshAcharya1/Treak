<?php

namespace App\Http\Controllers;

use App\Models\Trek;
use App\Models\User;
use Illuminate\Http\Request;

abstract class ApiController extends Controller
{
    /**
     * Get the authenticated user from the request.
     *
     * Prefers the user set by ApiAuthMiddleware via setUserResolver().
     * Falls back to manual token lookup for backwards compatibility.
     */
    protected function authenticate(Request $request): User
    {
        // Check if the middleware already resolved the user
        $user = $request->user();

        if ($user instanceof User) {
            return $user;
        }

        // Fallback: manual token authentication
        $token = $request->bearerToken();

        if (! $token) {
            abort(response()->json(['message' => 'Authorization token required.'], 401));
        }

        $user = User::where('api_token', '=', hash('sha256', $token))->first();

        if (! $user) {
            abort(response()->json(['message' => 'Invalid authorization token.'], 401));
        }

        return $user;
    }

    /**
     * Verify that the given user owns the given trek.
     * Aborts with a 403 JSON response if ownership check fails.
     */
    protected function authorizeOwner(User $user, Trek $trek): void
    {
        if ($trek->user_id !== $user->id) {
            abort($this->errorResponse('This resource does not belong to you.', 403));
        }
    }

    /**
     * Standardized success response.
     */
    protected function successResponse($data, string $message = null, int $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Standardized error response.
     */
    protected function errorResponse(string $message, int $code)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message
        ], $code);
    }
}
