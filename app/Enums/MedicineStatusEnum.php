<?php

namespace App\Enums;

enum MedicineStatusEnum: string
{
    case EXISTS = "exists";
    case OUT_OF_STOCK = "out_of_stock";

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
