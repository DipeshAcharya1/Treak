<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
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

        if (! $user || ! $user->isAdmin()) {
            return response()->json(['message' => 'Admin access required.'], 403);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
