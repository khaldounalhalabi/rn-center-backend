<?php

namespace App\Repositories;

use App\Models\Vacation;
use App\Repositories\Contracts\BaseRepository;
use Carbon\Carbon;

/**
 * @extends  BaseRepository<Vacation>
 */
class VacationRepository extends BaseRepository
{
    protected string $modelClass = Vacation::class;

    public function isVacation(string|Carbon $date, int $userId): bool
    {
        $data = Carbon::parse($date);
        return $this->globalQuery()
            ->where('from', '<=', $data->format('Y-m-d'))
            ->where('to', '>=', $data->format('Y-m-d'))
            ->where('user_id', $userId)
            ->exists();
    }

    public function getByUser(int $userId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginate(
            $this->globalQuery($relations, $countable)
                ->where('user_id', $userId)
        );
    }
}
