<?php

namespace App\Enums;

/**
 * Class RolesPermissionEnum
 */
class RolesPermissionEnum
{
    //**********ADMIN***********//
    public const ADMIN = [
        'role' => 'admin',
        'permissions' => [],
    ];
    //*************************//

    public const ALLROLES = [
        self::ADMIN['role'],
        self::DOCTOR['role'],
        self::CUSTOMER['role'],
        self::SECRETARY['role'],
        //add-all-your-enums-roles-here

    ];

    public const ALL = [
        self::ADMIN,
        self::DOCTOR,
        self::CUSTOMER,
        self::SECRETARY,
        //add-all-your-enums-here

    ];

    //**********DOCTOR***********//
    public const DOCTOR = [
        'role' => 'doctor',
        'permissions' => [],
    ];
    //*************************//

    //**********CUSTOMER***********//
    public const CUSTOMER = [
        'role' => 'customer',
        'permissions' => [],
    ];
    //*************************//

    //**********SECRETARY***********//
    const SECRETARY = [
        'role' => 'secretary',
        'permissions' => [
        ],
    ];
    //*************************//
}
