<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Owner = 'owner';
    case Guest = 'guest';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Amministratore',
            self::Owner => 'Proprietario',
            self::Guest => 'Ospite',
        };
    }
}
