<?php

namespace App\Services;

class CommissionService
{
    /**
     * Calcola lo split tra piattaforma e owner.
     *
     * @return array{commission_rate: float, commission_amount: float, owner_amount: float}
     */
    public function calculate(float $totalAmount): array
    {
        $rate = config('porty.commission_rate', 10.00);
        $commissionAmount = round($totalAmount * $rate / 100, 2);
        $ownerAmount = round($totalAmount - $commissionAmount, 2);

        return [
            'commission_rate' => $rate,
            'commission_amount' => $commissionAmount,
            'owner_amount' => $ownerAmount,
        ];
    }
}
