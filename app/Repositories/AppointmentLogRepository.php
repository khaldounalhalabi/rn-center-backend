<?php

namespace App\Repositories;

use App\Models\AppointmentLog;
use App\Repositories\Contracts\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as LaravelCollection;
use LaravelIdea\Helper\App\Models\_IH_AppointmentLog_C;

/**
 * @extends  BaseRepository<AppointmentLog>
 */
class AppointmentLogRepository extends BaseRepository
{
    public function __construct(AppointmentLog $appointmentLog)
    {
        parent::__construct($appointmentLog);
    }

    /**
     * @param       $appointmentId
     * @param array $relations
     * @return array|Collection<AppointmentLog>|LaravelCollection<AppointmentLog>|_IH_AppointmentLog_C
     */
    public function getByAppointmentId($appointmentId, array $relations = []): array|LaravelCollection|Collection|_IH_AppointmentLog_C
    {
        return $this->globalQuery($relations)->where('appointment_id', $appointmentId)->get();
    }
}
