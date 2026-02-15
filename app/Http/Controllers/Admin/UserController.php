<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
            ->when($request->role, fn($q, $r) => $q->where('role', $r))
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function toggleActive(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        return back()->with('success', "Utente {$user->name} " . ($user->is_active ? 'attivato' : 'disattivato') . '.');
    }
}
