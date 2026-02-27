<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RegisterController extends Controller
{
    /**
     * Register a new user and return JWT token.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'      => trim($request->first_name . ' ' . $request->last_name),
            'email'     => $request->email,
            'password'  => $request->password,
            'role'      => UserRole::from($request->role),
            'is_active' => true,
        ]);

        $token = auth('api')->login($user);

        return response()->json([
            'access' => $token,
            'user'   => new UserResource($user),
        ], 201);
    }
}
