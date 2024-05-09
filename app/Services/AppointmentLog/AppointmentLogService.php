<?php

namespace App\Services\AppointmentLog;

use App\Models\AppointmentLog;
use App\Services\Contracts\BaseService;
use App\Repositories\AppointmentLogRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as LaravelCollection;
use LaravelIdea\Helper\App\Models\_IH_AppointmentLog_C;

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

    /**
     * @param $appointmentId
     * @param array $relations
     * @return array|Collection<AppointmentLog>|LaravelCollection<AppointmentLog>|_IH_AppointmentLog_C
     */
    public function getAppointmentLogs($appointmentId, array $relations = []): Collection|_IH_AppointmentLog_C|array|LaravelCollection
    {
        return $this->repository->getByAppointmentId($appointmentId, $relations);
    }
}
