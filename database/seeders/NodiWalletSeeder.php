<?php

namespace Database\Seeders;

use App\Enums\NodoTransactionType;
use App\Models\NodiTransaction;
use App\Models\NodiWallet;
use App\Models\User;
use Illuminate\Database\Seeder;

class NodiWalletSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', '!=', 'admin')->get();

        if ($users->isEmpty()) {
            return;
        }

        // Importi specifici per gli owner (guadagni da sharing)
        $ownerEarnings = [65.00, 42.00];
        // Importi specifici per i guest (spese per booking sharing)
        $guestSpending = [35.00, 22.00];

        $ownerIndex = 0;
        $guestIndex = 0;

        foreach ($users as $user) {
            $welcomeBonus = 50.00;
            $balance = $welcomeBonus;
            $totalEarned = $welcomeBonus;
            $totalSpent = 0.00;

            // Crea o aggiorna il wallet con il saldo iniziale del bonus
            $wallet = NodiWallet::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'balance' => $balance,
                    'total_earned' => $totalEarned,
                    'total_spent' => $totalSpent,
                ],
            );

            // Salta le transazioni se il wallet esisteva già
            if (! $wallet->wasRecentlyCreated) {
                if ($user->role === 'owner') { $ownerIndex++; }
                if ($user->role === 'guest') { $guestIndex++; }
                continue;
            }

            // Transazione bonus di benvenuto
            NodiTransaction::create([
                'wallet_id' => $wallet->id,
                'booking_id' => null,
                'type' => NodoTransactionType::Bonus,
                'amount' => $welcomeBonus,
                'balance_after' => $balance,
                'description' => 'Bonus di benvenuto Porty - Registrazione completata',
                'metadata' => ['reason' => 'welcome_bonus', 'registration_date' => now()->toDateString()],
            ]);

            if ($user->role === 'owner') {
                // Owner: transazione Earned per sharing del posto barca
                $earned = $ownerEarnings[$ownerIndex] ?? 50.00;
                $ownerIndex++;

                $balance += $earned;
                $totalEarned += $earned;

                NodiTransaction::create([
                    'wallet_id' => $wallet->id,
                    'booking_id' => null,
                    'type' => NodoTransactionType::Earned,
                    'amount' => $earned,
                    'balance_after' => $balance,
                    'description' => 'Nodi guadagnati per condivisione posto barca',
                    'metadata' => ['source' => 'berth_sharing', 'period' => 'monthly'],
                ]);

                $wallet->update([
                    'balance' => $balance,
                    'total_earned' => $totalEarned,
                ]);
            }

            if ($user->role === 'guest') {
                // Guest: transazione Spent per prenotazione sharing
                $spent = $guestSpending[$guestIndex] ?? 30.00;
                $guestIndex++;

                $balance -= $spent;
                $totalSpent += $spent;

                NodiTransaction::create([
                    'wallet_id' => $wallet->id,
                    'booking_id' => null,
                    'type' => NodoTransactionType::Spent,
                    'amount' => $spent,
                    'balance_after' => $balance,
                    'description' => 'Nodi spesi per prenotazione ormeggio in sharing',
                    'metadata' => ['usage' => 'booking_sharing', 'berth_type' => 'standard'],
                ]);

                $wallet->update([
                    'balance' => $balance,
                    'total_spent' => $totalSpent,
                ]);
            }
        }
    }
}
