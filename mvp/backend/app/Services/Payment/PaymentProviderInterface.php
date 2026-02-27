<?php

namespace App\Services\Payment;

interface PaymentProviderInterface
{
    /**
     * Addebita un importo.
     *
     * @return array{success: bool, reference: string, message: string}
     */
    public function charge(float $amount, string $currency, array $metadata = []): array;

    /**
     * Rimborsa un pagamento.
     *
     * @return array{success: bool, reference: string, message: string}
     */
    public function refund(string $originalReference, float $amount, string $currency): array;

    /**
     * Verifica lo stato di un pagamento.
     *
     * @return array{status: string, amount: float, currency: string}
     */
    public function getStatus(string $reference): array;
}
