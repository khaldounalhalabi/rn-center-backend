<?php

namespace App\Services\User;

use App\Models\User;
use App\Services\Contracts\IBaseService;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @extends IBaseService<User>
 * Interface IUserService
 */
interface IUserService extends IBaseService
{
    /**
     * @param array $data
     * @param string|null $role
     * @param array $relations
     * @return array{user:User , token:string , refresh_token:string}|User|null
     */
    public function updateUserDetails(array $data, ?string $role = null, array $relations = []): array|User|null;

    /**
     * @param array $data
     * @param string|null $role
     * @param array $relations
     * @param array $additionalData
     * @return User|Authenticatable|array{user:User , token:string , refresh_token:string}|null
     */
    public function login(array $data, ?string $role = null, array $relations = [], array $additionalData = []): User|Authenticatable|array|null;

    /**
     * @param       $fcm_token
     * @return void
     */
    public function clearFcmTokenFromOtherUsers($fcm_token): void;

    /**
     * @return void
     */
    public function logout(): void;

    /**
     * @param array $relations
     * @return array{user:User , token:string , refresh_token:string}|null
     */
    public function refreshToken(array $relations = []): ?array;

    /**
     * @param array $data
     * @param string|null $role
     * @param array $relations
     * @return array{user:User , token:string , refresh_token:string}|User
     */
    public function register(array $data, ?string $role = null, array $relations = []): array|User;

    /**
     * @param string $email
     * @return bool|null
     */
    public function passwordResetRequest(string $email): ?bool;

    /**
     * @param            $email
     * @return User|null
     */
    public function getUserByEmail($email): ?User;

    /**
     * @param            $token
     * @return User|null
     */
    public function getUserByPasswordResetCode($token): ?User;

    /**
     * @param string $reset_password_code
     * @param string $password
     * @return bool
     */
    public function passwordReset(string $reset_password_code, string $password): bool;

    /**
     * @param string|null $role
     * @param array $relations
     * @return User|Authenticatable|null
     */
    public function userDetails(?string $role = null, array $relations = []): User|Authenticatable|null;

    /**
     * @param $verificationCode
     * @return bool
     */
    public function verifyCustomerEmail($verificationCode): bool;

    /**
     * @param User $user
     * @return void
     */
    public function requestVerificationCode(User $user): void;
}
