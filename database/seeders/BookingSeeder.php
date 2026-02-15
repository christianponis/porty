<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Berth;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $guests = User::where('role', 'guest')->get();
        $berths = Berth::with('owner')->get();

        if ($guests->isEmpty() || $berths->isEmpty()) {
            return;
        }

        $commissionRate = config('porty.commission_rate', 10.00);

        $bookings = [
            // Prenotazione confermata con transazioni
            [
                'guest_index' => 0,
                'berth_index' => 0,
                'start_offset' => 30,
                'days' => 7,
                'status' => BookingStatus::Confirmed,
                'with_transaction' => true,
            ],
            // Prenotazione in attesa
            [
                'guest_index' => 1,
                'berth_index' => 1,
                'start_offset' => 45,
                'days' => 3,
                'status' => BookingStatus::Pending,
                'with_transaction' => false,
            ],
            // Prenotazione confermata su altro posto
            [
                'guest_index' => 0,
                'berth_index' => 2,
                'start_offset' => 60,
                'days' => 14,
                'status' => BookingStatus::Confirmed,
                'with_transaction' => true,
            ],
            // Prenotazione cancellata
            [
                'guest_index' => 1,
                'berth_index' => 3,
                'start_offset' => 20,
                'days' => 5,
                'status' => BookingStatus::Cancelled,
                'with_transaction' => false,
            ],
            // Prenotazione in attesa su posto grande
            [
                'guest_index' => 0,
                'berth_index' => 4,
                'start_offset' => 90,
                'days' => 10,
                'status' => BookingStatus::Pending,
                'with_transaction' => false,
            ],
        ];

        foreach ($bookings as $data) {
            $guest = $guests[$data['guest_index']];
            $berth = $berths[$data['berth_index']] ?? $berths->first();

            $startDate = now()->addDays($data['start_offset']);
            $endDate = $startDate->copy()->addDays($data['days']);
            $totalPrice = $berth->price_per_day * $data['days'];

            // Applica prezzo settimanale se conviene
            if ($berth->price_per_week && $data['days'] >= 7) {
                $weeks = intdiv($data['days'], 7);
                $rem = $data['days'] % 7;
                $weeklyPrice = ($weeks * $berth->price_per_week) + ($rem * $berth->price_per_day);
                $totalPrice = min($totalPrice, $weeklyPrice);
            }

            $bookingData = [
                'berth_id' => $berth->id,
                'guest_id' => $guest->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_days' => $data['days'],
                'total_price' => round($totalPrice, 2),
                'status' => $data['status'],
                'guest_notes' => 'Prenotazione demo - barca a vela 10m.',
            ];

            if ($data['status'] === BookingStatus::Cancelled) {
                $bookingData['cancelled_by'] = 'guest';
                $bookingData['cancelled_at'] = now();
            }

            $booking = Booking::create($bookingData);

            // Crea transazioni per prenotazioni confermate
            if ($data['with_transaction']) {
                $commissionAmount = round($totalPrice * $commissionRate / 100, 2);
                $ownerAmount = round($totalPrice - $commissionAmount, 2);

                Transaction::create([
                    'booking_id' => $booking->id,
                    'type' => TransactionType::Payment,
                    'amount' => $totalPrice,
                    'currency' => 'EUR',
                    'status' => TransactionStatus::Completed,
                    'payment_method' => 'mock',
                    'commission_rate' => $commissionRate,
                    'commission_amount' => $commissionAmount,
                    'owner_amount' => $ownerAmount,
                    'provider_reference' => 'MOCK_PAY_SEED_' . $booking->id,
                ]);

                Transaction::create([
                    'booking_id' => $booking->id,
                    'type' => TransactionType::Commission,
                    'amount' => $commissionAmount,
                    'currency' => 'EUR',
                    'status' => TransactionStatus::Completed,
                    'payment_method' => 'internal',
                    'commission_rate' => $commissionRate,
                    'commission_amount' => $commissionAmount,
                    'owner_amount' => 0,
                ]);

                Transaction::create([
                    'booking_id' => $booking->id,
                    'type' => TransactionType::Payout,
                    'amount' => $ownerAmount,
                    'currency' => 'EUR',
                    'status' => TransactionStatus::Pending,
                    'payment_method' => 'internal',
                    'commission_rate' => $commissionRate,
                    'commission_amount' => 0,
                    'owner_amount' => $ownerAmount,
                ]);
            }
        }
    }
}
