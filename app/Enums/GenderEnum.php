<?php

namespace App\Enums;

enum GenderEnum: string
{
    case MALE = 'Male';
    case FEMALE = 'Female';

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
