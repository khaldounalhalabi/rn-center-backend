<?php

namespace App\Http\Controllers\API\v1;

use App\Exceptions\RoleDoesNotExistException;
use App\Http\Controllers\ApiController;
use App\Http\Requests\v1\AuthRequests\AuthLoginRequest;
use App\Http\Requests\v1\AuthRequests\AuthRegisterRequest;
use App\Http\Requests\v1\AuthRequests\CheckPasswordResetRequest;
use App\Http\Requests\v1\AuthRequests\RequestResetPasswordRequest;
use App\Http\Requests\v1\AuthRequests\ResendVerificationCodeRequest;
use App\Http\Requests\v1\AuthRequests\ResetPasswordRequest;
use App\Http\Requests\v1\AuthRequests\UpdateUserRequest;
use App\Http\Requests\v1\AuthRequests\VerifyUserRequest;
use App\Http\Resources\v1\UserResource;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;

class BaseAuthController extends ApiController
{
    private UserService $userService;
    private ?string $role = null;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->userService = UserService::make();
        $this->userService->setGuard();
    }

    public function roleHook(string $role)
    {
        $this->role = $role;
    }

    public function login(AuthLoginRequest $request)
    {
        //you can pass additional data as an array for the third parameter in the
        //login method and this data will be stored in the users table
        $result = $this->userService->login($request->validated(), $this->role, $this->relations);
        if (!$result || !is_array($result)) {
            return rest()->message(__('site.credentials_not_match_records'))->notAuthorized()->send();
        }

        [$user, $token, $refreshToken] = $result;

        if (!$user->isAdmin() && is_null($user->phone_verified_at)) {
            return rest()
                ->unverifiedPhone()
                ->send();
        }

        return rest()
            ->data([
                'user' => new UserResource($user),
                'token' => $token,
                'refresh_token' => $refreshToken,
            ])->message(__('site.successfully_logged_in'))
            ->ok()
            ->send();
    }

    public function logout()
    {
        $this->userService->logout();

        return rest()->ok()->message(__('site.logout_success'))->send();
    }

    public function refresh()
    {
        $result = $this->userService->refreshToken($this->relations);
        if (!$result || !is_array($result)) {
            return rest()->notAuthorized()->message(__('site.token_refreshed_failed'))->send();
        }
        [$user, $token, $refresh_token] = $result;

        if (!$user->isAdmin() && is_null($user->phone_verified_at)) {
            return rest()
                ->unverifiedPhone()
                ->send();
        }

        return rest()
            ->data([
                'user' => new UserResource($user),
                'token' => $token,
                'refresh_token' => $refresh_token,
            ])->message(__('site.token_refreshed_successfully'))
            ->ok()
            ->send();
    }

    /**
     * @throws RoleDoesNotExistException
     */
    public function register(AuthRegisterRequest $request)
    {
        $result = $this->userService->register($request->validated(), $this->relations, $this->role);

        if (!$result || !is_array($result)) {
            return rest()->noData()->message(__('site.failed'))->send();
        }

        [$user, $token, $refresh_token] = $result;
        return rest()
            ->data([
                'user' => new UserResource($user),
                'token' => $token,
                'refresh_token' => $refresh_token,
            ])->message(__('site.registered_successfully'))
            ->ok()
            ->send();
    }

    public function passwordResetRequest(RequestResetPasswordRequest $request)
    {
        $result = $this->userService->passwordResetRequest($request->phone);
        if ($result) {
            return rest()->ok()->message(__('site.password_reset_code_sent'))->send();
        }

        return rest()->notFound()->message(__('site.wrong_phone'))->send();
    }

    public function checkPasswordResetCode(CheckPasswordResetRequest $request)
    {
        return rest()->ok()->message(__('site.code_correct'))->send();
    }

    public function passwordReset(ResetPasswordRequest $request)
    {
        $result = $this->userService->passwordReset($request->reset_password_code, $request->password);
        if ($result) {
            return rest()->ok()->message(__('site.password_reset_successful'))->send();
        }

        return rest()->notFound()->message(__('site.code_incorrect'))->send();
    }

    public function updateUserDetails(UpdateUserRequest $request)
    {
        $result = $this->userService->updateUserDetails($request->validated(), $this->relations, $this->role);

        if (!$result || !is_array($result)) {
            return rest()->notAuthorized()->message(__('site.unauthorized_user'))->send();
        }

        [$user, $token, $refresh_token] = $result;
        return rest()->data([
            'user' => new UserResource($user),
            'token' => $token,
            'refresh_token' => $refresh_token,
        ])->updateSuccess()
            ->ok()
            ->send();
    }

    public function userDetails()
    {
        $user = $this->userService->userDetails($this->relations, $this->role);

        if ($user) {
            return rest()->data($user)->getSuccess()->ok()->send();
        } else {
            return rest()->notAuthorized()->message(__('site.unauthorized_user'))->send();
        }
    }

    public function verifyUser(VerifyUserRequest $request)
    {
        $result = $this->userService->verifyUser($request->validated(), $this->role);
        return rest()
            ->when(
                $result,
                fn($rest) => $rest->ok()->message(__('site.verified_successfully')),
                fn($rest) => $rest->noData()
            )->send();
    }

    public function resendVerificationCode(ResendVerificationCodeRequest $request)
    {
        $result = $this->userService->resendVerificationCode($request->phone, $this->role);
        return rest()
            ->when(
                $result,
                fn($rest) => $rest->ok()->message(trans('site.code_sent')),
                fn($rest) => $rest->noData()
            )->send();
    }

    public function storeFcmToken(Request $request)
    {
        $token = $request->fcm_token;

        $user = auth()->user();

        $user->fcm_token = $token;
        $user->save();

        return rest()
            ->message('Token Stored Successfully')
            ->ok()
            ->send();
    }

    public function getUserFcmToken()
    {
        return rest()
            ->data(['fcm_token' => user()?->fcm_token])
            ->message(trans('site.get_successfully'))
            ->ok()
            ->send();
    }
}

