<?php

namespace App\Services;

use App\Enums\NodoTransactionType;
use App\Models\Berth;
use App\Models\Booking;
use App\Models\NodiTransaction;
use App\Models\NodiWallet;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NodiService
{
    public function getOrCreateWallet(User $user): NodiWallet
    {
        return $user->getOrCreateWallet();
    }

    public function creditNodi(
        NodiWallet $wallet,
        float $amount,
        NodoTransactionType $type,
        ?Booking $booking = null,
        ?string $description = null,
    ): NodiTransaction {
        return DB::transaction(function () use ($wallet, $amount, $type, $booking, $description) {
            $wallet = NodiWallet::lockForUpdate()->find($wallet->id);
            $wallet->increment('balance', $amount);
            $wallet->increment('total_earned', $amount);

            return NodiTransaction::create([
                'wallet_id' => $wallet->id,
                'booking_id' => $booking?->id,
                'type' => $type,
                'amount' => $amount,
                'balance_after' => $wallet->fresh()->balance,
                'description' => $description,
            ]);
        });
    }

    public function debitNodi(
        NodiWallet $wallet,
        float $amount,
        NodoTransactionType $type,
        ?Booking $booking = null,
        ?string $description = null,
    ): NodiTransaction {
        return DB::transaction(function () use ($wallet, $amount, $type, $booking, $description) {
            $wallet = NodiWallet::lockForUpdate()->find($wallet->id);

            if ($wallet->balance < $amount) {
                throw new \RuntimeException('Saldo Nodi insufficiente. Disponibile: ' . $wallet->balance . ', richiesto: ' . $amount);
            }

            $wallet->decrement('balance', $amount);
            $wallet->increment('total_spent', $amount);

            return NodiTransaction::create([
                'wallet_id' => $wallet->id,
                'booking_id' => $booking?->id,
                'type' => $type,
                'amount' => -$amount,
                'balance_after' => $wallet->fresh()->balance,
                'description' => $description,
            ]);
        });
    }

    public function calculateNodiForBooking(Berth $berth, int $totalDays): float
    {
        $baseValue = $berth->nodi_value_per_day ?? config('porty.nodi.base_value_per_day', 10.00);
        $multiplier = $berth->getNodiMultiplier();

        return round($baseValue * $totalDays * $multiplier, 2);
    }

    public function getMultiplier(Berth $berth): int
    {
        return $berth->getNodiMultiplier();
    }
}
