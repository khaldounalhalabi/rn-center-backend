<?php

namespace App\Repositories;

use App\Enums\RolesPermissionEnum;
use App\Models\User;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

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

    public function getSecretaries(array $relations = [], array $countable = []): ?array
    {
        return $this->paginate(
            $this->globalQuery($relations, $countable)
                ->byRole(RolesPermissionEnum::SECRETARY['role'])
        );
    }

    public function employees(array $relations = [], array $countable = []): ?array
    {
        return $this->paginate(
            $this->globalQuery($relations, $countable)
                ->byRole([RolesPermissionEnum::SECRETARY['role'], RolesPermissionEnum::DOCTOR['role']])
        );
    }
}
