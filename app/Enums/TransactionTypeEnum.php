<?php

namespace App\Enums;

enum TransactionTypeEnum: string
{
    case INCOME = "income";
    case OUTCOME = "outcome";

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
