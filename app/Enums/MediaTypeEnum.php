<?php

namespace App\Enums;

use App\Models\Clinic;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\User;

enum MediaTypeEnum: string
{
    public const TYPES = [
        User::class => 'user',
        Clinic::class => 'clinic',
        Speciality::class => 'speciality',
        Service::class => 'service',
        Customer::class => 'customer',
    ];

    case SINGLE = 'single';
    case MULTIPLE = 'multiple';

    /**
     * @param string $key
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
