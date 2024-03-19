<?php

namespace App\Enums;

enum WeekDayEnum: string
{
    case SATURDAY = 'saturday';
    case SUNDAY = 'sunday';
    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';
    case FRIDAY = 'friday';

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
