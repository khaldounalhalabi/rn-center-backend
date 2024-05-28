<?php

namespace App\Enums;

enum OfferTypeEnum: string
{
    case PERCENTAGE = "percentage";
    case FIXED = "fixed";

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
