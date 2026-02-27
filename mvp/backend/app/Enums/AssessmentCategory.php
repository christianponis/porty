<?php

namespace App\Enums;

enum AssessmentCategory: string
{
    case Infrastructure = 'infrastructure';
    case Services = 'services';
    case Security = 'security';
    case Location = 'location';
    case LandServices = 'land_services';
    case Sustainability = 'sustainability';

    public function label(): string
    {
        return match ($this) {
            self::Infrastructure => 'Infrastruttura',
            self::Services => 'Servizi',
            self::Security => 'Sicurezza',
            self::Location => 'Posizione',
            self::LandServices => 'Servizi a terra',
            self::Sustainability => 'Sostenibilita',
        };
    }

    public function weight(): float
    {
        return match ($this) {
            self::Infrastructure => 0.25,
            self::Services => 0.20,
            self::Security => 0.15,
            self::Location => 0.20,
            self::LandServices => 0.10,
            self::Sustainability => 0.10,
        };
    }
}
