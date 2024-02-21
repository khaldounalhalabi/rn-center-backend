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
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function getUserByEmail($email): User|null
    {
        return User::where('email', $email)->first();
    }

    public function getUserByPasswordResetCode($token): User|null
    {
        return User::where('reset_password_code', $token)->first();
    }

    public function getByFcmToken($fcm_token): Collection|array|_IH_User_C
    {
        return User::where('fcm_token', $fcm_token)->get();
    }
}
