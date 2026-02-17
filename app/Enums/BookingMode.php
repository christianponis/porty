<?php

namespace App\Enums;

enum BookingMode: string
{
    case Rental = 'rental';
    case Sharing = 'sharing';
    case SharingCompensation = 'sharing_compensation';

    public function label(): string
    {
        return match ($this) {
            self::Rental => 'Affitto (EUR)',
            self::Sharing => 'Sharing (Nodi)',
            self::SharingCompensation => 'Sharing + Compensazione',
        };
    }
}
