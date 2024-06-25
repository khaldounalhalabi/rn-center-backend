<?php

namespace App\Enums;

use App\Models\Clinic;
use App\Models\Hospital;
use App\Models\Offer;
use App\Models\PatientProfile;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\SystemOffer;
use App\Models\User;

enum MediaTypeEnum: string
{
    public const TYPES = [
        User::class           => 'user',
        Clinic::class         => 'clinic',
        Hospital::class       => 'hospital',
        Speciality::class     => 'speciality',
        PatientProfile::class => 'patient_profile',
        Service::class        => 'service',
        Offer::class          => 'offer',
        SystemOffer::class    => 'system_offer',
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
