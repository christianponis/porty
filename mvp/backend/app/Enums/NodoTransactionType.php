<?php

namespace App\Enums;

enum NodoTransactionType: string
{
    case Earned = 'earned';
    case Spent = 'spent';
    case Bonus = 'bonus';
    case Adjustment = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::Earned => 'Guadagnati',
            self::Spent => 'Spesi',
            self::Bonus => 'Bonus',
            self::Adjustment => 'Correzione',
        };
    }
}
