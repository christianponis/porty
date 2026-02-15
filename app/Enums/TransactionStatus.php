<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'In attesa',
            self::Completed => 'Completata',
            self::Failed => 'Fallita',
        };
    }
}
