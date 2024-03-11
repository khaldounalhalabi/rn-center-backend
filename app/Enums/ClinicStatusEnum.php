<?php

namespace App\Enums;

enum ClinicStatusEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'in active';

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
