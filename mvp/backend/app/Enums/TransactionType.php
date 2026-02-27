<?php

namespace App\Enums;

enum TransactionType: string
{
    case Payment = 'payment';
    case Refund = 'refund';
    case Commission = 'commission';
    case Payout = 'payout';

    public function label(): string
    {
        return match ($this) {
            self::Payment => 'Pagamento',
            self::Refund => 'Rimborso',
            self::Commission => 'Commissione',
            self::Payout => 'Pagamento owner',
        };
    }
}
