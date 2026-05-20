<?php

declare(strict_types=1);

namespace App\Enums;

enum ExternalImportSourceType: string
{
    case Sikayetvar = 'sikayetvar';
    case SikayetvarInstitution = 'sikayetvar_institution';

    public function label(): string
    {
        return match ($this) {
            self::Sikayetvar => __('Şikayetvar (genel)'),
            self::SikayetvarInstitution => __('Şikayetvar (kurum bağlantılı)'),
        };
    }
}
