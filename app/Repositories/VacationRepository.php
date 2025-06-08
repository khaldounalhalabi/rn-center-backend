<?php

namespace App\Repositories;

use App\Enums\VacationStatusEnum;
use App\Models\Vacation;
use App\Repositories\Contracts\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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
            ->where('status', VacationStatusEnum::APPROVED->value)
            ->exists();
    }

    public function getByUser(int $userId, array $relations = [], array $countable = []): ?array
    {
        return $this->paginate(
            $this->globalQuery($relations, $countable)
                ->where('user_id', $userId)
        );
    }

    /**
     * @param int|null $userId
     * @param array    $relations
     * @param array    $countable
     * @return Collection<Vacation>
     */
    public function activeVacations(?int $userId = null, array $relations = [], array $countable = []): Collection
    {
        return $this->globalQuery($relations, $countable)
            ->where('from', '>=', now()->format('Y-m-d'))
            ->where('status', VacationStatusEnum::APPROVED->value)
            ->when(isset($userId), fn(Builder $query) => $query->where('user_id', $userId))
            ->get();
    }
}
