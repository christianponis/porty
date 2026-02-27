<?php

namespace App\Enums;

enum ConventionCategory: string
{
    case Commercial = 'commercial';
    case Technical = 'technical';
    case Tourism = 'tourism';
    case Health = 'health';
    case Transport = 'transport';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Commercial => 'Commerciale',
            self::Technical  => 'Tecnico',
            self::Tourism    => 'Turismo',
            self::Health     => 'Salute',
            self::Transport  => 'Trasporto',
            self::Other      => 'Altro',
        };
    }
}
