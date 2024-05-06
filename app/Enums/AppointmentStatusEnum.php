<?php

namespace App\Enums;

enum AppointmentStatusEnum: string
{
    case PENDING = 'pending';
    case CHECKIN = 'checkin';
    case CHECKOUT = 'checkout';
    case CANCELLED = 'cancelled';
    case BOOKED = 'booked';
    case COMPLETED = 'completed';

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
