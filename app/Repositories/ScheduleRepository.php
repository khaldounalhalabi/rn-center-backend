<?php

namespace App\Repositories;

use App\Models\Clinic;
use App\Models\Hospital;
use App\Models\Schedule;
use App\Repositories\Contracts\BaseRepository;

use Illuminate\Database\Eloquent\Collection;

/**
 * @extends  BaseRepository<Schedule>
 * <Schedule>
 */
class ScheduleRepository extends BaseRepository
{
    protected string $modelClass = Schedule::class;


    /**
     * @param class-string $schedulableType
     * @param int|null     $schedulableId
     * @return Collection<Schedule>|array<Schedule>
     */
    public function getSchedulesByType(string $schedulableType, ?int $schedulableId = null): Collection|array
    {
        return $this->globalQuery()
            ->where('schedulable_type', $schedulableType)
            ->when(isset($schedulableId), fn($q) => $q->where('schedulable_id', $schedulableId))
            ->get();
    }

    /**
     * @param array $data
     * @param array $relations
     * @return Schedule
     */
    public function updateOrCreate(array $data, array $relations = []): Schedule
    {
        return Schedule::updateOrCreate($data)->load($relations);
    }

    /**
     * @param int                           $schedulableId
     * @param class-string<Clinic|Hospital> $schedulableType
     * @return bool|null
     */
    public function deleteAll(int $schedulableId, string $schedulableType): ?bool
    {
        return $this->globalQuery()
            ->where('schedulable_id', $schedulableId)
            ->where('schedulable_type', $schedulableType)
            ->delete();
    }

    public function insert(array $data): bool
    {
        return Schedule::insert($data);
    }
}
