<?php

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Booking;
use App\Models\Transaction;
use App\Services\Payment\PaymentProviderInterface;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private PaymentProviderInterface $paymentProvider,
        private CommissionService $commissionService,
    ) {}

    /**
     * Processa il pagamento quando una prenotazione viene confermata.
     * Crea 3 transazioni: payment (guest), commission (piattaforma), payout (owner).
     */
    public function processBookingPayment(Booking $booking): Transaction
    {
        $amount = (float) $booking->total_price;
        $currency = config('porty.currency', 'EUR');

        // Calcola split commissioni
        $split = $this->commissionService->calculate($amount);

        // Esegui addebito tramite provider
        $result = $this->paymentProvider->charge($amount, $currency, [
            'booking_id' => $booking->id,
            'guest_id' => $booking->guest_id,
        ]);

        $status = $result['success'] ? TransactionStatus::Completed : TransactionStatus::Failed;

        return DB::transaction(function () use ($booking, $amount, $currency, $split, $result, $status) {
            // 1. Transazione pagamento (dal guest)
            $payment = Transaction::create([
                'booking_id' => $booking->id,
                'type' => TransactionType::Payment,
                'amount' => $amount,
                'currency' => $currency,
                'status' => $status,
                'payment_method' => config('porty.payment.default_provider', 'mock'),
                'commission_rate' => $split['commission_rate'],
                'commission_amount' => $split['commission_amount'],
                'owner_amount' => $split['owner_amount'],
                'provider_reference' => $result['reference'],
                'metadata' => ['provider_message' => $result['message']],
            ]);

            // Solo se il pagamento e andato a buon fine, crea le transazioni di split
            if ($result['success']) {
                // 2. Transazione commissione (quota piattaforma)
                Transaction::create([
                    'booking_id' => $booking->id,
                    'type' => TransactionType::Commission,
                    'amount' => $split['commission_amount'],
                    'currency' => $currency,
                    'status' => TransactionStatus::Completed,
                    'payment_method' => 'internal',
                    'commission_rate' => $split['commission_rate'],
                    'commission_amount' => $split['commission_amount'],
                    'owner_amount' => 0,
                    'provider_reference' => null,
                ]);

                // 3. Transazione payout (quota owner)
                Transaction::create([
                    'booking_id' => $booking->id,
                    'type' => TransactionType::Payout,
                    'amount' => $split['owner_amount'],
                    'currency' => $currency,
                    'status' => TransactionStatus::Pending, // il payout verra processato dopo
                    'payment_method' => 'internal',
                    'commission_rate' => $split['commission_rate'],
                    'commission_amount' => 0,
                    'owner_amount' => $split['owner_amount'],
                    'provider_reference' => null,
                ]);
            }

            return $payment;
        });
    }

    /**
     * Processa il rimborso quando una prenotazione confermata viene cancellata.
     */
    public function processBookingRefund(Booking $booking): ?Transaction
    {
        // Cerca la transazione di pagamento originale
        $originalPayment = $booking->transactions()
            ->where('type', TransactionType::Payment)
            ->where('status', TransactionStatus::Completed)
            ->first();

        if (! $originalPayment) {
            return null;
        }

        $amount = (float) $originalPayment->amount;
        $currency = $originalPayment->currency;

        $result = $this->paymentProvider->refund(
            $originalPayment->provider_reference,
            $amount,
            $currency
        );

        $status = $result['success'] ? TransactionStatus::Completed : TransactionStatus::Failed;

        return Transaction::create([
            'booking_id' => $booking->id,
            'type' => TransactionType::Refund,
            'amount' => $amount,
            'currency' => $currency,
            'status' => $status,
            'payment_method' => $originalPayment->payment_method,
            'commission_rate' => $originalPayment->commission_rate,
            'commission_amount' => $originalPayment->commission_amount,
            'owner_amount' => $originalPayment->owner_amount,
            'provider_reference' => $result['reference'],
            'metadata' => [
                'original_payment_id' => $originalPayment->id,
                'provider_message' => $result['message'],
            ],
        ]);
    }
}
