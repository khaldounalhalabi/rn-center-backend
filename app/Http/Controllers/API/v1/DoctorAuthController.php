<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\RolesPermissionEnum;

class DoctorAuthController extends BaseAuthController
{
    public function __construct()
    {
        parent::__construct();
        $this->roleHook(RolesPermissionEnum::DOCTOR['role']);
        $this->relations = ['clinic', 'roles', 'permissions'];
    }
}
