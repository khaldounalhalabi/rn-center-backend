<?php

namespace App\Enums;

enum AppointmentStatusEnum: string
{
    case PENDING = 'pending';
    case BLOCKED = 'blocked';
    case CHECKIN = 'checkin';
    case CHECKOUT = 'checkout';
    case CANCELLED = 'cancelled';

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
