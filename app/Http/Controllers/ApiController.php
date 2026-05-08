<?php

namespace App\Http\Controllers;

use App\Models\Trek;
use App\Models\User;
use Illuminate\Http\Request;

abstract class ApiController extends Controller
{
    protected function authenticate(Request $request): User
    {
        $token = $request->bearerToken();

        if (! $token) {
            abort(401, 'Authorization token required.');
        }

        $user = User::where('api_token', '=', hash('sha256', $token))->first();

        if (! $user) {
            abort(401, 'Invalid authorization token.');
        }

        return $user;
    }

    protected function authorizeOwner(User $user, Trek $trek): void
    {
        if ($trek->user_id !== $user->id) {
            abort(response()->json(['message' => 'This resource does not belong to you.'], 403));
        }
    }
}
