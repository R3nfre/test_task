<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $validToken = env('API_TOKEN');

        if (!$token || $token !== $validToken) {
            return response()->json([
                'status' => 'error',
                'code' => 403,
                'message' => 'Invalid token'
            ], 403);
        }

        return $next($request);
    }
}
