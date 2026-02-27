<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage: middleware('role:admin') or middleware('role:owner,guest')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth('api')->user();

        if (! $user) {
            return response()->json([
                'message' => 'Non autenticato.',
            ], 401);
        }

        $userRole = $user->role->value;

        if (! in_array($userRole, $roles)) {
            return response()->json([
                'message' => 'Non autorizzato. Ruolo richiesto: ' . implode(' o ', $roles),
            ], 403);
        }

        return $next($request);
    }
}
