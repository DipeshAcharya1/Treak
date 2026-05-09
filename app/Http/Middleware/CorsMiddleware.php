<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request and attach CORS headers.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Handle preflight OPTIONS requests immediately
        if ($request->isMethod('OPTIONS')) {
            return response('', 204)
                ->header('Access-Control-Allow-Origin', $this->getAllowedOrigin($request))
                ->header('Access-Control-Allow-Methods', implode(', ', config('cors.allowed_methods', ['*'])))
                ->header('Access-Control-Allow-Headers', implode(', ', config('cors.allowed_headers', ['*'])))
                ->header('Access-Control-Max-Age', config('cors.max_age', 86400));
        }

        $response = $next($request);

        // Attach CORS headers to the response
        $response->headers->set('Access-Control-Allow-Origin', $this->getAllowedOrigin($request));
        $response->headers->set('Access-Control-Allow-Methods', implode(', ', config('cors.allowed_methods', ['*'])));
        $response->headers->set('Access-Control-Allow-Headers', implode(', ', config('cors.allowed_headers', ['*'])));
        $response->headers->set('Access-Control-Expose-Headers', implode(', ', config('cors.exposed_headers', [])));

        if (config('cors.supports_credentials', false)) {
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }

        return $response;
    }

    /**
     * Determine the allowed origin for the current request.
     */
    protected function getAllowedOrigin(Request $request): string
    {
        $allowedOrigins = config('cors.allowed_origins', ['*']);
        $requestOrigin = $request->header('Origin', '');

        // If wildcard is allowed, return wildcard
        if (in_array('*', $allowedOrigins)) {
            return '*';
        }

        // Check if the request origin is in the allowed list
        if (in_array($requestOrigin, $allowedOrigins)) {
            return $requestOrigin;
        }

        // Default: return the first allowed origin
        return $allowedOrigins[0] ?? '';
    }
}
