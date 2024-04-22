<?php

namespace App\Enums;

enum AppointmentTypeEnum: string
{
    case MANUAL = 'manual';
    case ONLINE = 'online';

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
