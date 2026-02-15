<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'In attesa',
            self::Confirmed => 'Confermata',
            self::Cancelled => 'Cancellata',
            self::Completed => 'Completata',
        };
    }
}
