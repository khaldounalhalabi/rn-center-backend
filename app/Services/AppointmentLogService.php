<?php

namespace App\Services;

use App\Models\AppointmentLog;
use App\Repositories\AppointmentLogRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as LaravelCollection;
use LaravelIdea\Helper\App\Models\_IH_AppointmentLog_C;

/**
 * @property AppointmentLogRepository $repository
 * @extends BaseService<AppointmentLog>
 */
class AppointmentLogService extends BaseService
{
    use Makable;

    protected string $repositoryClass = AppointmentLogRepository::class;

    /**
     * @param       $appointmentId
     * @param array $relations
     * @return array|Collection<AppointmentLog>|LaravelCollection<AppointmentLog>|_IH_AppointmentLog_C
     */
    public function getAppointmentLogs($appointmentId, array $relations = []): Collection|_IH_AppointmentLog_C|array|LaravelCollection
    {
        return $this->repository->getByAppointmentId($appointmentId, $relations);
    }
}
