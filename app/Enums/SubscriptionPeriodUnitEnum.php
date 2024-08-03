<?php

namespace App\Enums;

enum SubscriptionPeriodUnitEnum: string
{
    case DAY = "day";
    case MONTH = "month";

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
