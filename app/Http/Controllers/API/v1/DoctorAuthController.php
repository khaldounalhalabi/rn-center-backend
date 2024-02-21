<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\RolesPermissionEnum;
use App\Services\User\IUserService;

class DoctorAuthController extends BaseAuthController
{
    public function __construct(IUserService $userService)
    {
        parent::__construct($userService);

        $this->roleHook(RolesPermissionEnum::DOCTOR['role']);
    }
}
