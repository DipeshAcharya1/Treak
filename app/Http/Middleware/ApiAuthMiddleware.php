<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Authenticate the request using the Bearer token and set the user resolver.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Authorization token required.'], 401);
        }

        $user = User::where('api_token', '=', hash('sha256', $token))->first();

        if (! $user) {
            return response()->json(['message' => 'Invalid authorization token.'], 401);
        }

        // Make the authenticated user available via $request->user()
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
