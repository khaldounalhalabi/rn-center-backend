<?php

namespace App\Services\Schedule;

use App\Models\Schedule;
use App\Services\Contracts\BaseService;
use App\Repositories\ScheduleRepository;

/**
 * @implements IScheduleService<Schedule>
 * Class UserService
 */
class ScheduleService extends BaseService implements IScheduleService
{
    /**
     * ScheduleService constructor.
     *
     * @param ScheduleRepository $repository
     */
    public function __construct(ScheduleRepository $repository)
    {
        parent::__construct($repository);
    }
}
