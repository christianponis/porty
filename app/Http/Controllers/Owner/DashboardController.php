<?php

namespace App\Http\Controllers\Owner;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();
        $berthIds = $user->berths()->pluck('id');

        $stats = [
            'total_berths' => $user->berths()->count(),
            'active_berths' => $user->berths()->where('is_active', true)->count(),
            'pending_bookings' => Booking::whereIn('berth_id', $berthIds)->where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::whereIn('berth_id', $berthIds)->where('status', 'confirmed')->count(),
            'total_bookings' => Booking::whereIn('berth_id', $berthIds)->count(),
            'total_earnings' => Transaction::whereHas('booking', fn($q) => $q->whereIn('berth_id', $berthIds))
                ->where('type', TransactionType::Payout)
                ->where('status', TransactionStatus::Completed)
                ->sum('amount'),
            'pending_payouts' => Transaction::whereHas('booking', fn($q) => $q->whereIn('berth_id', $berthIds))
                ->where('type', TransactionType::Payout)
                ->where('status', TransactionStatus::Pending)
                ->sum('amount'),
            'total_volume' => Booking::whereIn('berth_id', $berthIds)
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('total_price'),
        ];

        $recent_bookings = Booking::with(['berth.port', 'guest'])
            ->whereIn('berth_id', $berthIds)
            ->latest()
            ->take(5)
            ->get();

        $berths = $user->berths()
            ->with('port')
            ->withCount(['bookings', 'availabilities'])
            ->get();

        return view('owner.dashboard', compact('stats', 'recent_bookings', 'berths'));
    }
}
