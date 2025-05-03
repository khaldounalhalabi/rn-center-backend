<?php

namespace App\Traits;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

trait BaseEnum
{
    public static function getAllValues(array|string $except = null): array
    {
        return $except
            ? array_filter(
                array_column(self::cases(), 'value'),
                fn($item) => is_array($except) ? !in_array($item, $except) : $item != $except,
            )
            : array_column(self::cases(), 'value');
    }

    public static function validationRule(): In
    {
        return Rule::in(self::getAllValues());
    }
}
