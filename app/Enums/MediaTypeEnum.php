<?php

namespace App\Enums;

use App\Models\User;

enum MediaTypeEnum: string
{
    public const TYPES = [
        User::class => 'user',
    ];

    case SINGLE = 'single';
    case MULTIPLE = 'multiple';

    /**
     * @param  string $key
     * @return string
     */
    public static function getType(string $key): string
    {
        return self::TYPES[$key];
    }

    public static function getAllValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
