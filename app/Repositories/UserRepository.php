<?php

namespace App\Repositories;

use App\Enums\RolesPermissionEnum;
use App\Models\User;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\App\Models\_IH_User_C;

/**
 * @extends BaseRepository<User>
 */
class UserRepository extends BaseRepository
{
    protected string $modelClass = User::class;

    public function getUserByPhone(string $phone): ?User
    {
        return User::where('phone', $phone)->first();
    }

    /**
     * @param array{email:string , phone_numbers:string[]} $data
     * @return null|User
     */
    public function getExistCustomerUser(array $data = []): ?User
    {
        return $this->globalQuery()->when(isset($data['email']), fn(Builder $query) => $query->where('email', $data['email']))
            ->when(isset($data['phone_numbers'])
                , fn(Builder $query) => $query->whereHas('phoneNumbers', function (Builder $query) use ($data) {
                    $query->whereIn('phone', $data['phone_numbers'])
                        ->where('phoneable_type', User::class);
                }))
            ->byRole(RolesPermissionEnum::CUSTOMER['role'])
            ->first();
    }
}
