<?php

namespace App\Repositories;

use App\Models\Vacation;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Vacation>
 */
class VacationRepository extends BaseRepository
{
    protected string $modelClass = Vacation::class;

    public function getByUser(int $userId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginate(
            $this->globalQuery($relations, $countable)
                ->where('user_id', $userId)
        );
    }
}
