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
