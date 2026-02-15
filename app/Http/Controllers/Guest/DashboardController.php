<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'active_bookings' => $user->bookings()->whereIn('status', ['pending', 'confirmed'])->count(),
            'pending_bookings' => $user->bookings()->where('status', 'pending')->count(),
            'confirmed_bookings' => $user->bookings()->where('status', 'confirmed')->count(),
            'completed_bookings' => $user->bookings()->where('status', 'completed')->count(),
            'total_spent' => $user->bookings()
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('total_price'),
        ];

        $upcoming_bookings = $user->bookings()
            ->with(['berth.port', 'berth.owner'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->take(5)
            ->get();

        $past_bookings = $user->bookings()
            ->with(['berth.port'])
            ->whereIn('status', ['completed', 'cancelled'])
            ->latest()
            ->take(3)
            ->get();

        return view('guest.dashboard', compact('stats', 'upcoming_bookings', 'past_bookings'));
    }
}
