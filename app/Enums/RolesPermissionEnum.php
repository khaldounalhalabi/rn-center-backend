<?php

namespace App\Enums;

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


    public const ALL_ROLES = [
        self::ADMIN['role'],
        self::DOCTOR['role'],
        self::CUSTOMER['role'],
        self::CLINIC_STAFF['role'],
        //add-all-your-enums-roles-here
    ];

    public const ALL = [
        self::ADMIN,
        self::DOCTOR,
        self::CUSTOMER,
        //add-all-your-enums-here


    ];

    //**********DOCTOR***********//
    public const DOCTOR = [
        'role'        => 'doctor',
        'permissions' => [],
    ];
    //*************************//

    //**********CLINIC STAFF***********//
    public const CLINIC_STAFF = [
        'role'        => 'clinic-staff',
        'permissions' => [
            'manage-schedules',
            'manage-holidays',
            'manage-services',
            'manage-offers',
            'manage-patients',
            'manage-medicines',
            'manage-appointments',
            'edit-clinic-profile',
            'show-clinic-profile',
            'manage-transactions',
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
