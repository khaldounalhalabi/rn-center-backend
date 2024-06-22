<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\RolesPermissionEnum;
use App\Http\Requests\Customer\RequestVerificationCode;
use App\Http\Requests\Customer\VerifyEmailRequest;

class CustomerAuthController extends BaseAuthController
{
    public function __construct()
    {
        parent::__construct();

        $this->roleHook([
            RolesPermissionEnum::CUSTOMER['role'],
        ]);
        $this->relations = ['customer', 'media', 'phones'];
    }

    public function verifyCustomerEmail(VerifyEmailRequest $request)
    {
        $data = $request->validated();
        if (!isset($data['verification_code'])) {
            return $this->apiResponse(null, self::STATUS_NOT_FOUND, __('wrong_verification_code'));
        }

        $result = $this->userService->verifyCustomerEmail($data['verification_code']);
        if ($result) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.code_correct'));
        }

        return $this->apiResponse(null, self::STATUS_NOT_FOUND, __('wrong_verification_code'));
    }

    public function requestVerificationCode(RequestVerificationCode $request)
    {
        $data = $request->validated();
        $user = $this->userService->getUserByEmail($data['email']);

        if (!$user) {
            return $this->noData(false);
        }

        $this->userService->requestVerificationCode($user);

        return $this->apiResponse(true, self::STATUS_OK, __('site.email_verification_code_sent'));
    }
}
