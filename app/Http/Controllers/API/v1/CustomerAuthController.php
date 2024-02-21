<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\RolesPermissionEnum;
use App\Services\User\IUserService;

class CustomerAuthController extends BaseAuthController
{
    public function __construct(IUserService $userService)
    {
        parent::__construct($userService);

        $this->roleHook(RolesPermissionEnum::CUSTOMER['role']);
        $this->relations = ['customer' , 'media'];
    }
}
