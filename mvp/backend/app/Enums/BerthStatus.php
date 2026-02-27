<?php

namespace App\Enums;

enum BerthStatus: string
{
    case Available = 'available';
    case Occupied = 'occupied';
    case Maintenance = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Disponibile',
            self::Occupied => 'Occupato',
            self::Maintenance => 'In manutenzione',
        };
    }
}
