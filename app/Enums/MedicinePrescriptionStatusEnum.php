<?php

namespace App\Enums;

enum MedicinePrescriptionStatusEnum: string
{
    case GIVEN = 'given';
    case NOT_GIVEN = 'not_given';

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
