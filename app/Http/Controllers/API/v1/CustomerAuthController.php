<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\RolesPermissionEnum;
use App\Http\Requests\Customer\CustomerPasswordResetRequest;
use App\Http\Requests\Customer\CustomerRequestResetPasswordRequest;
use App\Http\Requests\Customer\LoginRequest;
use App\Http\Requests\Customer\RequestVerificationCodeByPhoneRequest;
use App\Http\Requests\Customer\ValidateResetPasswordCodeRequest;
use App\Http\Requests\Customer\VerifyPhoneNumberRequest;
use App\Http\Resources\PhoneNumberResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\PhoneNumberService;

class CustomerAuthController extends BaseAuthController
{
    public function __construct()
    {
        parent::__construct();

        $this->roleHook(RolesPermissionEnum::CUSTOMER['role']);
        $this->relations = ['customer', 'roles'];
    }
}
