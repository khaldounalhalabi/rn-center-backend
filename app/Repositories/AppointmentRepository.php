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
}
