<?php

namespace App\Enums;

use App\Models\Asset;
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
        Asset::class => 'asset',
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
