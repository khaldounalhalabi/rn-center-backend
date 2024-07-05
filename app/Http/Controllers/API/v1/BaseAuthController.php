<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Http\Requests\AuthRequests\AuthLoginRequest;
use App\Http\Requests\AuthRequests\AuthRegisterRequest;
use App\Http\Requests\AuthRequests\CheckPasswordResetRequest;
use App\Http\Requests\AuthRequests\RequestResetPasswordRequest;
use App\Http\Requests\AuthRequests\ResetPasswordRequest;
use App\Http\Requests\AuthRequests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;

class BaseAuthController extends ApiController
{
    protected UserService $userService;
    private ?array $roles = null;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->userService = UserService::make();
        $this->relations = ['media', 'phoneNumbers', 'address.city', 'roles', 'permissions'];
    }

    public function roleHook(array $roles = [])
    {
        $this->roles = $roles;
    }

    public function login(AuthLoginRequest $request)
    {
        //you can pass additional data as an array for the third parameter in the
        //login method and this data will be stored in the users table
        [$user, $token, $refresh_token] = $this->userService->login($request->validated(), $this->roles, $this->relations);
        if (!$user) {
            return $this->apiResponse(null, self::STATUS_UNAUTHORIZED, __('site.credentials_not_match_records'));
        }

        if ($user->is_archived) {
            return $this->apiResponse(null, self::STATUS_ARCHIVED, __('site.archived'));
        }

        if ($user->is_blocked) {
            return $this->apiResponse(null, self::STATUS_BLOCKED, __('site.blocked'));
        }

        if ($user->isClinic() && !$user->clinic?->hasActiveSubscription()) {
            return $this->apiResponse(null, self::STATUS_EXPIRED_SUBSCRIPTION, __('site.expired_subscription'));
        }

        return $this->apiResponse([
            'user'          => new UserResource($user),
            'token'         => $token,
            'refresh_token' => $refresh_token
        ], self::STATUS_OK, __('site.successfully_logged_in'));
    }

    public function logout()
    {
        $this->userService->logout();

        return $this->apiResponse(null, self::STATUS_OK, __('site.logout_success'));
    }

    public function refresh()
    {
        [$user, $token, $refresh_token] = $this->userService->refreshToken($this->relations);
        if ($user) {
            return $this->apiResponse([
                'user'          => new UserResource($user),
                'token'         => $token,
                'refresh_token' => $refresh_token
            ], self::STATUS_OK, __('site.token_refreshed_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_UNAUTHORIZED, __('site.token_refreshed_failed'));
    }

    public function register(AuthRegisterRequest $request)
    {
        [$user, $token, $refresh_token] = $this->userService->register($request->validated(), $this->roles, $this->relations);

        return $this->apiResponse([
            'user'          => new UserResource($user),
            'token'         => $token,
            'refresh_token' => $refresh_token
        ], self::STATUS_OK, __('site.registered_successfully'));
    }

    public function passwordResetRequest(RequestResetPasswordRequest $request)
    {
        $result = $this->userService->passwordResetRequest($request->email);
        if ($result) {
            return $this->apiResponse(null, self::STATUS_OK, __('site.password_reset_code_sent'));
        }

        return $this->apiResponse(null, self::STATUS_OK, __('site.wrong_email'));
    }

    public function checkPasswordResetCode(CheckPasswordResetRequest $request)
    {
        return $this->apiResponse(null, self::STATUS_OK, __('site.code_correct'));
    }

    public function passwordReset(ResetPasswordRequest $request)
    {
        $result = $this->userService->passwordReset($request->reset_password_code, $request->password);
        if ($result) {
            return $this->apiResponse(true, self::STATUS_OK, __('site.password_reset_successful'));
        }

        return $this->apiResponse(null, self::STATUS_BAD_REQUEST, __('site.code_incorrect'));
    }

    public function updateUserDetails(UpdateUserRequest $request)
    {
        [$user, $token, $refresh_token] = $this->userService->updateUserDetails($request->validated(), $this->roles, $this->relations);

        if ($user) {
            return $this->apiResponse([
                'user'          => new UserResource($user),
                'token'         => $token,
                'refresh_token' => $refresh_token
            ], self::STATUS_OK, __('site.update_successfully'));
        }

        return $this->apiResponse(null, self::STATUS_BAD_REQUEST, __('site.unauthorized_user'));
    }

    public function userDetails()
    {
        $user = $this->userService->userDetails($this->roles, $this->relations);

        if ($user) {
            return $this->apiResponse(new UserResource($user), self::STATUS_OK, __('site.get_successfully'));
        }
        return $this->apiResponse(null, self::STATUS_BAD_REQUEST, __('site.unauthorized_user'));
    }

    public function checkRole()
    {
        return $this->apiResponse(
            auth()->user()?->roles()->first()->name,
            self::STATUS_OK,
            __('site.get_successfully')
        );
    }

    public function storeFcmToken(Request $request)
    {
        try {
            $token = $request->fcm_token;

            $user = auth()->user();

            $user->fcm_token = $token;

            $user->save();

            return response()->json([
                'message' => 'Token Stored Successfully'
            ]);
        } catch (Exception) {
            return response()->json([
                'message' => "There Is Been An Error Registering FCM Token"
            ], 403);
        }
    }

    public function getUserFcmToken()
    {
        return $this->apiResponse([
            'fcm_token' => auth()->user()?->fcm_token
        ], self::STATUS_OK, __('site.success'));
    }
}
