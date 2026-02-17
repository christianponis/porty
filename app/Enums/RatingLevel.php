<?php

namespace App\Enums;

enum RatingLevel: string
{
    case Grey = 'grey';
    case Blue = 'blue';
    case Gold = 'gold';

    public function label(): string
    {
        return match ($this) {
            self::Grey => 'Ancora Grigia',
            self::Blue => 'Ancora Blu',
            self::Gold => 'Ancora Dorata',
        };
    }
}
