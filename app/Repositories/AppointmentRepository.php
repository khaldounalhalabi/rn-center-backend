<?php

namespace App\Repositories;

use App\Enums\AppointmentStatusEnum;
use App\Models\Appointment;
use App\Repositories\Contracts\BaseRepository;
use Carbon\Carbon;
use DateTime;

/**
 * @extends  BaseRepository<Appointment>
 */
class AppointmentRepository extends BaseRepository
{
    protected string $modelClass = Appointment::class;

    /**
     * @param             $clinicId
     * @param string|null $date
     * @return Appointment|null
     */
    public function getClinicLastAppointmentInDay($clinicId, ?string $date = null): ?Appointment
    {
        if (!$date) $date = now()->format('Y-m-d');

        return $this->globalQuery()
            ->where('date', $date)
            ->where('clinic_id', $clinicId)
            ->orderBy('appointment_sequence', 'DESC')
            ->first();
    }

    /**
     * @param       $clinicId
     * @param array $relations
     * @param int   $perPage
     * @return array|null
     */
    public function getByClinic($clinicId, array $relations = [], int $perPage = 10): ?array
    {
        $data = $this->globalQuery($relations)
            ->where('clinic_id', $clinicId)
            ->paginate();

        if (count($data)) {
            return [
                'data'            => $data,
                'pagination_data' => $this->formatPaginateData($data)
            ];
        }

        return null;
    }

    /**
     * @param                        $clinicId
     * @param string|Carbon|DateTime $date
     * @param array                  $data
     * @return bool|int
     */
    public function updatePreviousCheckinClinicAppointments($clinicId, string|Carbon|DateTime $date, array $data): bool|int
    {
        return $this->globalQuery()
            ->where('clinic_id', $clinicId)
            ->where('date', '>', $date)
            ->where('status', AppointmentStatusEnum::CHECKIN->value)
            ->update($data);
    }
}
