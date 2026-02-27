<?php

namespace App\Services\Payment;

use Illuminate\Support\Str;

class MockPaymentProvider implements PaymentProviderInterface
{
    public function charge(float $amount, string $currency, array $metadata = []): array
    {
        // Simula un ritardo minimo
        usleep(100_000); // 100ms

        $reference = 'MOCK_PAY_' . Str::upper(Str::random(12));

        return [
            'success' => true,
            'reference' => $reference,
            'message' => "Pagamento mock di {$currency} {$amount} completato.",
        ];
    }

    public function refund(string $originalReference, float $amount, string $currency): array
    {
        usleep(100_000);

        $reference = 'MOCK_REF_' . Str::upper(Str::random(12));

        return [
            'success' => true,
            'reference' => $reference,
            'message' => "Rimborso mock di {$currency} {$amount} completato.",
        ];
    }

    public function getStatus(string $reference): array
    {
        return [
            'status' => 'completed',
            'amount' => 0,
            'currency' => config('porty.currency', 'EUR'),
        ];
    }
}
