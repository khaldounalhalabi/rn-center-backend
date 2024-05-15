<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Repositories\Contracts\BaseRepository;

/**
 * @extends  BaseRepository<Appointment>
 */
class AppointmentRepository extends BaseRepository
{
    public function __construct(Appointment $appointment)
    {
        parent::__construct($appointment);
    }

    /**
     * @param string|null $date
     * @return Appointment|null
     */
    public function getLastAppointmentInDay(?string $date = null): ?Appointment
    {
        if (!$date) $date = now()->format('Y-m-d');

        return $this->globalQuery()
            ->where('date', $date)
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
                'data' => $data,
                'pagination_data' => $this->formatPaginateData($data)
            ];
        }

        return null;
    }
}
