<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Paginate users with optional search filter.
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 15));

        return response()->json(UserResource::collection($users)->response()->getData(true));
    }

    /**
     * Update a user's role.
     */
    public function updateRole(Request $request, int $userId): JsonResponse
    {
        $request->validate([
            'role' => ['required', 'in:admin,owner,guest'],
        ]);

        $user = User::findOrFail($userId);
        $user->update([
            'role' => UserRole::from($request->role),
        ]);

        return response()->json([
            'message' => 'Ruolo aggiornato con successo.',
            'data' => new UserResource($user->fresh()),
        ]);
    }
}
