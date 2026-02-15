<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with(['booking.berth.port', 'booking.berth.owner', 'booking.guest'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->latest()
            ->paginate(20);

        // Statistiche aggregate
        $stats = [
            'total_payments' => Transaction::where('type', TransactionType::Payment)
                ->where('status', TransactionStatus::Completed)->sum('amount'),
            'total_commissions' => Transaction::where('type', TransactionType::Commission)
                ->where('status', TransactionStatus::Completed)->sum('amount'),
            'total_payouts' => Transaction::where('type', TransactionType::Payout)
                ->where('status', TransactionStatus::Completed)->sum('amount'),
            'total_refunds' => Transaction::where('type', TransactionType::Refund)
                ->where('status', TransactionStatus::Completed)->sum('amount'),
            'pending_payouts' => Transaction::where('type', TransactionType::Payout)
                ->where('status', TransactionStatus::Pending)->sum('amount'),
        ];

        return view('admin.transactions.index', compact('transactions', 'stats'));
    }
}
