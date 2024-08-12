<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\RolesPermissionEnum;
use App\Http\Requests\Customer\CustomerPasswordResetRequest;
use App\Http\Requests\Customer\CustomerRequestResetPasswordRequest;
use App\Http\Requests\Customer\LoginRequest;
use App\Http\Requests\Customer\RequestVerificationCodeByPhoneRequest;
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

        $this->roleHook([
            RolesPermissionEnum::CUSTOMER['role'],
        ]);
        $this->relations = ['customer', 'media', 'phones', 'address', 'address.city'];
    }

    public function verifyCustomerPhone(VerifyPhoneNumberRequest $request)
    {
        $data = $request->validated();
        if (!isset($data['verification_code'])) {
            return $this->apiResponse(null, self::STATUS_NOT_FOUND, __('wrong_verification_code'));
        }

        $result = PhoneNumberService::make()->verify($data['verification_code']);

        if ($result) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.code_correct'));
        }

        return $this->apiResponse(null, self::STATUS_NOT_FOUND, __('wrong_verification_code'));
    }

    public function requestVerificationCodeByPhone(RequestVerificationCodeByPhoneRequest $request)
    {
        $data = $request->validated();
        $phone = PhoneNumberService::make()->getByPhone($data['phone_number']);
        $user = $phone->phoneable_type == User::class ? $phone->phoneable : null;

        if (!$user) {
            return $this->noData(false);
        }

        PhoneNumberService::make()->requestNumberVerificationCode($phone->phone, $user);

        return $this->apiResponse(true, self::STATUS_OK, __('site.sms_verification_code_sent'));
    }

    public function requestResetPasswordCodeByPhone(CustomerRequestResetPasswordRequest $request)
    {
        $phone = $request->validated()["phone_number"] ?? null;
        if (!$phone) {
            return $this->noData();
        }
        $result = $this->userService->passwordResetRequestByPhone($phone);

        if ($result) {
            return $this->apiResponse(null, self::STATUS_OK, __('site.password_reset_code_sent'));
        } else {
            return $this->apiResponse(null, self::STATUS_OK, __('site.wrong_phone'));
        }
    }

    public function passwordResetByPhone(CustomerPasswordResetRequest $request)
    {
        $data = $request->validated();
        $result = $this->userService->passwordResetByPhone($data['verification_code'], $data['password']);
        if ($result) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.password_reset_successful'));
        } else {
            return $this->apiResponse(null, self::STATUS_BAD_REQUEST, __('site.code_incorrect'));
        }
    }

    public function loginByPhone(LoginRequest $request)
    {
        $result = $this->userService->loginByPhone($request->validated());

        if (!$result) {
            return $this->apiResponse(null, self::STATUS_UNAUTHORIZED, __('site.credentials_not_match_records'));
        }

        [$user, $token, $refreshToken, $phone] = $result;

        if (!$user->hasRole(RolesPermissionEnum::CUSTOMER['role'])) {
            return $this->apiResponse(null, self::STATUS_UNAUTHORIZED, __('site.unauthorized'));
        }

        if ($user->is_archived) {
            return $this->apiResponse(null, self::STATUS_ARCHIVED, __('site.archived'));
        }

        if ($user->isBlocked()) {
            return $this->apiResponse(null, self::STATUS_BLOCKED, __('site.blocked'));
        }

        if (!$phone->is_verified) {
            return $this->apiResponse(null, self::STATUS_UNVERIFIED_PHONE_NUMBER, __('site.un_verified_phone'));
        }

        return $this->apiResponse([
            'user'          => new UserResource($user),
            'token'         => $token,
            'refresh_token' => $refreshToken,
            'phone'         => new PhoneNumberResource($phone),
        ], self::STATUS_OK, __('site.successfully_logged_in'));
    }
}
