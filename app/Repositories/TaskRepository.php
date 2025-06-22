<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends  BaseRepository<Task>
 */
class TaskRepository extends BaseRepository
{
    protected string $modelClass = Task::class;

    public function getByUser(int $userId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginate(
            $this->globalQuery($relations, $countable)
                ->whereHas('users', function (Builder|User $user) use ($userId) {
                    $user->where('users.id', $userId);
                })
        );
    }
}
