<?php

namespace App\Repositories;

use App\Models\Clinic;
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
     * @param int|null $schedulableId
     * @return Collection<Schedule>|array<Schedule>
     */
    public function getByClinic(?int $schedulableId = null): Collection|array
    {
        return $this->globalQuery()
            ->where('scheduleable_id', $schedulableId)
            ->where('scheduleable_type', Clinic::class)
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
     * @param int $clinicId
     * @return bool|null
     */
    public function deleteByClinic(int $clinicId): ?bool
    {
        return $this->globalQuery()
            ->where('scheduleable_id', $clinicId)
            ->where('scheduleable_type', Clinic::class)
            ->delete();
    }

    public function insert(array $data): bool
    {
        return Schedule::insert($data);
    }
}
