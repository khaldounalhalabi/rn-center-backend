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

    /**
     * @param AppointmentStatusEnum[] $except
     * @return array
     */
    public static function getAllValues(array $except = []): array
    {
        return array_column(
            array_filter(self::cases(), fn(AppointmentStatusEnum $status) => !in_array($status, $except)),
            'value'
        );
    }
}
