<?php

namespace App\Enums;

enum GenderEnum: string
{
    case MALE = 'male';
    case FEMALE = 'female';

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
