<?php

namespace App\Enums;

enum CertificationStatus: string
{
    case Pending = 'pending';
    case Scheduled = 'scheduled';
    case Completed = 'completed';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'In attesa',
            self::Scheduled => 'Programmata',
            self::Completed => 'Completata',
            self::Expired => 'Scaduta',
        };
    }
}
