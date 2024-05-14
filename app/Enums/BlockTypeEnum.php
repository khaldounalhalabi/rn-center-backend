<?php

namespace App\Enums;

enum BlockTypeEnum: string
{
    case EMAIL = "email";
    case PHONE = "phone";
    case FULL_NAME = "full_name";

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}


