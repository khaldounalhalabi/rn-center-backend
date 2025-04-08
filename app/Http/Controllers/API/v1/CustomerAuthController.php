<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\RolesPermissionEnum;

class CustomerAuthController extends BaseAuthController
{
    public function __construct()
    {
        parent::__construct();

        $this->roleHook(RolesPermissionEnum::CUSTOMER['role']);
        $this->relations = ['customer', 'roles'];
    }
}
