<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Berth;
use App\Models\Booking;
use App\Models\Port;
use App\Models\Transaction;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'total_users' => User::count(),
            'total_owners' => User::where('role', 'owner')->count(),
            'total_guests' => User::where('role', 'guest')->count(),
            'total_ports' => Port::count(),
            'total_berths' => Berth::count(),
            'active_berths' => Berth::active()->count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'total_volume' => Transaction::where('type', TransactionType::Payment)
                ->where('status', TransactionStatus::Completed)->sum('amount'),
            'total_commissions' => Transaction::where('type', TransactionType::Commission)
                ->where('status', TransactionStatus::Completed)->sum('amount'),
            'pending_payouts' => Transaction::where('type', TransactionType::Payout)
                ->where('status', TransactionStatus::Pending)->sum('amount'),
        ];

        $recent_bookings = Booking::with(['berth.port', 'guest'])
            ->latest()
            ->take(5)
            ->get();

        $recent_transactions = Transaction::with(['booking.berth', 'booking.guest'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_bookings', 'recent_transactions'));
    }
}
