<?php

namespace App\Services\AppointmentLog;

use App\Models\AppointmentLog;
use App\Services\Contracts\BaseService;
use App\Repositories\AppointmentLogRepository;

/**
 * @implements IAppointmentLogService<AppointmentLog>
 * Class UserService
 */
class AppointmentLogService extends BaseService implements IAppointmentLogService
{
    /**
     * AppointmentLogService constructor.
     *
     * @param AppointmentLogRepository $repository
     */
    public function __construct(AppointmentLogRepository $repository)
    {
        parent::__construct($repository);
    }
}
