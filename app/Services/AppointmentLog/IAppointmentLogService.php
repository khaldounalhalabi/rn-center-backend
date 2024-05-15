<?php

namespace App\Services\AppointmentLog;

use App\Services\Contracts\IBaseService;
use App\Models\AppointmentLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as LaravelCollection;
use LaravelIdea\Helper\App\Models\_IH_AppointmentLog_C;

/**
 * @extends IBaseService<AppointmentLog>
 * Interface IUserService
 */
interface IAppointmentLogService extends IBaseService
{
    /**
     * @param       $appointmentId
     * @param array $relations
     * @return array|Collection<AppointmentLog>|LaravelCollection<AppointmentLog>|_IH_AppointmentLog_C
     */
    public function getAppointmentLogs($appointmentId, array $relations = []): Collection|_IH_AppointmentLog_C|array|LaravelCollection;
}
