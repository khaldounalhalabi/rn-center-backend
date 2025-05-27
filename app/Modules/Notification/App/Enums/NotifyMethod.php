<?php

namespace App\Modules\Notification\App\Enums;

enum NotifyMethod
{
    case ONE;
    case MANY;
    case BY_ROLE;
    case TO_QUERY;

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
