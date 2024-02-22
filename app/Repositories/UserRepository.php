<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;
use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\App\Models\_IH_User_C;

/**
 * @extends BaseRepository<User>
 */
class UserRepository extends BaseRepository implements IBaseRepository
{
    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * @param $email
     * @return User|null
     */
    public function getUserByEmail($email): User|null
    {
        return User::where('email', $email)->first();
    }

    /**
     * @param $token
     * @return User|null
     */
    public function getUserByPasswordResetCode($token): User|null
    {
        return User::where('reset_password_code', $token)->first();
    }

    /**
     * @param $fcm_token
     * @return Collection|array|_IH_User_C
     */
    public function getByFcmToken($fcm_token): Collection|array|_IH_User_C
    {
        return User::where('fcm_token', $fcm_token)->get();
    }

    /**
     * @param string $verificationCode
     * @return User|null
     */
    public function getUserByVerificationCode(string $verificationCode): ?User
    {
        return User::where('verification_code', $verificationCode)->first();
    }
}
