<?php

namespace App\Repositories;

use App\Enums\RolesPermissionEnum;
use App\Models\User;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\App\Models\_IH_User_C;

/**
 * @extends BaseRepository<User>
 */
class UserRepository extends BaseRepository implements IBaseRepository
{
    protected string $modelClass = User::class;

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

    /**
     * @param array{email:string , phone_numbers:string[]} $data
     * @return null|User
     */
    public function getExistCustomerUser(array $data = []): ?User
    {
        return User::when(isset($data['email']), fn(Builder $query) => $query->where('email', $data['email']))
            ->when(isset($data['phone_numbers'])
                , fn(Builder $query) => $query->whereHas('phoneNumbers', function (Builder $query) use ($data) {
                    $query->whereIn('phone', $data['phone_numbers'])
                        ->where('phoneable_type', User::class);
                }))
            ->byRole(RolesPermissionEnum::CUSTOMER['role'])
            ->first();
    }
}
