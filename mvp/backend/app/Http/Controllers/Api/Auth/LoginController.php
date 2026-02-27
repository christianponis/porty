<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    /**
     * Authenticate user and return JWT token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Credenziali non valide.',
            ], 401);
        }

        $user = auth('api')->user();

        if (! $user->is_active) {
            auth('api')->logout();

            return response()->json([
                'message' => 'Account disattivato. Contatta l\'assistenza.',
            ], 403);
        }

        return response()->json([
            'access' => $token,
            'user'   => new UserResource($user),
        ]);
    }

    /**
     * Refresh the JWT token.
     */
    public function refresh(): JsonResponse
    {
        $token = auth('api')->refresh();

        return response()->json([
            'access' => $token,
        ]);
    }

    /**
     * Invalidate the JWT token (logout).
     */
    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json([
            'message' => 'Logout effettuato con successo.',
        ]);
    }
}
