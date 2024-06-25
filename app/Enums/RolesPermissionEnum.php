<?php

namespace App\Enums;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ClinicEmployee;
use App\Models\ClinicHoliday;
use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Offer;
use App\Models\Schedule;
use App\Models\Service;

/**
 * Class RolesPermissionEnum
 */
class RolesPermissionEnum
{
    //**********ADMIN***********//
    public const ADMIN = [
        'role'        => 'admin',
        'permissions' => [],
    ];
    //*************************//


    public const ALLROLES = [
        self::ADMIN['role'],
        self::DOCTOR['role'],
        self::CUSTOMER['role'],
        self::CLINIC_EMPLOYEE['role'],
        //add-all-your-enums-roles-here
    ];

    public const ALL = [
        self::ADMIN,
        self::DOCTOR,
        self::CUSTOMER,
        self::CLINIC_EMPLOYEE,
        //add-all-your-enums-here


    ];

    //**********DOCTOR***********//
    public const DOCTOR = [
        'role'        => 'doctor',
        'permissions' => [],
    ];
    //*************************//

    //**********CLINIC STAFF***********//
    public const CLINIC_EMPLOYEE = [
        'role'        => 'clinic-employee',
        'permissions' => [
            'manage-schedules'    => Schedule::class,
            'manage-holidays'     => ClinicHoliday::class,
            'manage-services'     => Service::class,
            'manage-offers'       => Offer::class,
            'manage-patients'     => Customer::class,
            'manage-medicines'    => Medicine::class,
            'manage-appointments' => Appointment::class,
            'edit-clinic-profile' => Clinic::class,
            'manage-employees'    => ClinicEmployee::class,
        ],
    ];
    //*************************//

    //**********CUSTOMER***********//
    public const CUSTOMER = [
        'role'        => 'customer',
        'permissions' => [],
    ];
    //*************************//
}
