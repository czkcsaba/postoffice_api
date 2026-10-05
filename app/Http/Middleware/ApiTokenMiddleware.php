<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $validToken = (string) config('api.token');

        if (!$token || !hash_equals($validToken, $token)) {
            return response()->json([
                'message' => 'Érvénytelen vagy hiányzó API token.'
            ], 401);
        }

        return $next($request);
    }
}
