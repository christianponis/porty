<?php

namespace App\Enums;

enum AssessmentStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case Approved = 'approved';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Bozza',
            self::Submitted => 'Inviata',
            self::Approved => 'Approvata',
        };
    }
}
