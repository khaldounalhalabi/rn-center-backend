<?php

namespace  App\Repositories;

use App\Models\Schedule;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;

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
}
