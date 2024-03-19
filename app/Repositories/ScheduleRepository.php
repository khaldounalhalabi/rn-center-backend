<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends  BaseRepository<Schedule>
 * @implements IBaseRepository<Schedule>
 */
class ScheduleRepository extends BaseRepository
{
    public function __construct(Schedule $schedule)
    {
        parent::__construct($schedule);
    }

    /**
     * @param class-string $schedulableType
     * @param int|null $schedulableId
     * @return Collection<Schedule>|array<Schedule>
     */
    public function getSchedulesByType(string $schedulableType, ?int $schedulableId = null): Collection|array
    {
        return $this->globalQuery()
            ->where('schedulable_type', $schedulableType)
            ->when(isset($schedulableId), fn($q) => $q->where('schedulable_id', $schedulableId))
            ->get();
    }
}
