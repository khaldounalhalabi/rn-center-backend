<?php

namespace App\Enums;

enum ClinicTransactionTypeEnum: string
{
    case SYSTEM_DEBT = "system_debt";
    case DEBT_TO_ME = "debt_to_me";
    case INCOME = "income";
    case OUTCOME = "outcome";

    /**
     * @param ClinicTransactionTypeEnum[] $except
     * @return array
     */
    public static function getAllValues(array $except = []): array
    {
        return array_column(
            array_filter(self::cases(), fn($item) => !in_array($item, $except)),
            'value'
        );
    }
}
