<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\RolesPermissionEnum;

class AdminAuthController extends BaseAuthController
{
    public function __construct()
    {
        parent::__construct();

        $this->roleHook(RolesPermissionEnum::ADMIN['role']);
    }
}
