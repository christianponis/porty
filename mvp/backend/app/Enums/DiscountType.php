<?php

namespace App\Enums;

enum DiscountType: string
{
    case Percentage = 'percentage';
    case Fixed = 'fixed';
    case Free = 'free';

    public function label(): string
    {
        return match ($this) {
            self::Percentage => 'Percentuale',
            self::Fixed      => 'Fisso (EUR)',
            self::Free       => 'Gratuito',
        };
    }
}
