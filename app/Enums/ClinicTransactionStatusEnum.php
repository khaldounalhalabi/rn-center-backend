<?php

namespace App\Enums;

enum ClinicTransactionStatusEnum: string
{
    case PENDING = "pending";
    case DONE = "done";

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
