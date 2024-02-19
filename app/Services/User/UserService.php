<?php

namespace App\Services\User;

use App\Models\User;
use App\Notifications\ResetPasswordCodeEmail;
use App\Repositories\UserRepository;
use App\Services\Contracts\BaseService;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Class UserService
 */
class UserService extends BaseService implements IUserService
{
    /**
     * @var string
     */
    private string $guard = 'api';

    /**
     * UserService constructor.
     *
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);
    }

    /**
     * @param array $data
     * @param string|null $role
     * @return array|null
     */

    public function updateUserDetails(array $data, ?string $role): ?array
    {
        $user = auth($this->guard)->user();

        if (!$user) {
            return null;
        }

        if (!$user->hasRole($role)) {
            return null;
        }

        $user = $this->repository->update($data, $user->id);

        $token = auth($this->guard)->login($user);
        $refresh_token = auth($this->guard)->setTTL(env('JWT_REFRESH_TTL', 20160))->refresh();

        return ['user' => $user, 'token' => $token, 'refresh_token' => $refresh_token];
    }

    /**
     * @param array $data
     * @param string $role
     * @param array $additionalData
     * @return array|null
     */
    public function login(array $data, string $role, array $additionalData = []): ?array
    {
        $token = auth($this->guard)->attempt([
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        if (!$token) {
            return null;
        }

        $user = auth($this->guard)->user();

        if (!$user->hasRole($role)) {
            return null;
        }

        if (isset($data['fcm_token']) && $data['fcm_token']) {
            $this->clearFcmTokenFromOtherUsers($data['fcm_token']);
            $user->fcm_token = $data['fcm_token'];
            $user->save();
        }

        foreach ($additionalData as $value) {
            $user->{$additionalData} = $value;
            $user->save();
        }

        $refresh_token = auth($this->guard)->setTTL(ttl: env('JWT_REFRESH_TTL', 20160))->refresh();

        return [
            'user' => $user,
            'token' => $token,
            'refresh_token' => $refresh_token,
        ];
    }

    /**
     * @param $fcm_token
     * @return void
     */
    public function clearFcmTokenFromOtherUsers($fcm_token): void
    {
        $users = $this->repository->getByFcmToken($fcm_token);
        foreach ($users as $user) {
            $user->fcm_token = null;
            $user->save();
        }
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        $auth_user = auth($this->guard)->user();
        auth($this->guard)->logout();
        $auth_user->fcm_token = null;
        $auth_user->save();
    }

    /**
     * @return array|null
     */
    public function refresh_token(): ?array
    {
        try {
            $user = auth($this->guard)->user();
            $token = auth($this->guard)->setTTL(env('JWT_TTL', 10080))->refresh();
            $refresh_token = auth($this->guard)->setTTL(env('JWT_REFRESH_TTL', 20160))->refresh();

            return ['user' => $user, 'token' => $token, 'refresh_token' => $refresh_token];
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @param array $data
     * @param string|null $role
     * @return array
     */
    public function register(array $data, ?string $role): array
    {
        $user = $this->repository->create($data);
        $user->assignRole($role);
        $token = auth($this->guard)->login($user);
        $refresh_token = auth($this->guard)->setTTL(env('JWT_REFRESH_TTL', 20160))->refresh();

        return ['user' => $user, 'token' => $token, 'refresh_token' => $refresh_token];
    }

    /**
     * @param string $email
     * @return bool|null
     */
    public function passwordResetRequest(string $email): ?bool
    {
        $user = $this->getUserByEmail($email);

        if ($user) {
            do {
                $code = sprintf('%06d', mt_rand(1, 999999));
                $temp_user = $this->getUserByPasswordResetCode($code);
            } while ($temp_user != null);

            $user->reset_password_code = $code;
            $user->save();

            try {
                $user->notify(new ResetPasswordCodeEmail($code));
            } catch (Exception) {
                return null;
            }

            return true;
        }

        return null;
    }

    /**
     * @param $email
     * @return User|null
     */
    public function getUserByEmail($email): ?User
    {
        return $this->repository->getUserByEmail($email);
    }

    /**
     * @param $token
     * @return User|null
     */
    public function getUserByPasswordResetCode($token): ?User
    {
        return $this->repository->getUserByPasswordResetCode($token);
    }

    /**
     * @param string $reset_password_code
     * @param string $password
     * @return bool|null
     */
    public function passwordReset(string $reset_password_code, string $password): ?bool
    {
        $user = $this->getUserByPasswordResetCode($reset_password_code);

        if ($user) {
            $user->password = $password;
            $user->reset_password_code = null;
            $user->save();

            return true;
        }

        return null;
    }

    /**
     * @param string|null $role
     * @return User|Authenticatable|null
     */
    public function userDetails(?string $role = null): User|Authenticatable|null
    {
        if ($role && !auth($this->guard)->user()->hasRole($role)) {
            return null;
        }

        return auth($this->guard)->user();
    }
}
