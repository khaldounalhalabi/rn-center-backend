<?php

namespace App\Repositories;

use App\Models\Clinic;
use App\Models\Schedule;
use App\Models\User;
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
     * @param int                       $schedulableId
     * @param class-string<Clinic|User> $type
     * @return Collection<Schedule>|array<Schedule>
     */
    public function getByScheduleable(int $schedulableId, string $type = Clinic::class): Collection|array
    {
        return $this->globalQuery()
            ->where('scheduleable_id', $schedulableId)
            ->where('scheduleable_type', $type)
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
     * @param int                       $scheduleableId
     * @param class-string<Clinic|User> $type
     * @return bool|null
     */
    public function deleteByScheduleable(int $scheduleableId, string $type = Clinic::class): ?bool
    {
        return $this->globalQuery()
            ->where('scheduleable_id', $scheduleableId)
            ->where('scheduleable_type', $type)
            ->delete();
    }

    public function insert(array $data): bool
    {
        return Schedule::insert($data);
    }
}
