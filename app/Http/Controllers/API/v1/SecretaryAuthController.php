<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\RolesPermissionEnum;

class SecretaryAuthController extends BaseAuthController
{
    public function __construct()
    {
        parent::__construct();
        $this->roleHook(RolesPermissionEnum::SECRETARY['role']);
        $this->relations = ['roles' , 'permissions'];
    }
}
