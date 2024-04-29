<?php

namespace  App\Repositories;

use App\Models\AppointmentLog;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\Contracts\IBaseRepository;

/**
 * @extends  BaseRepository<AppointmentLog>
 */
class AppointmentLogRepository extends BaseRepository
{
    public function __construct(AppointmentLog $appointmentLog)
    {
        parent::__construct($appointmentLog);
    }
}
