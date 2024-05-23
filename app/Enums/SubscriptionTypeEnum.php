<?php

namespace App\Enums;

enum SubscriptionTypeEnum: string
{
    case BOOKING_COST_BASED = "Booking Cost Based Subscription";
    case MONTHLY_PAID_BASED = "Monthly Paid Based Subscription";

    public function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
