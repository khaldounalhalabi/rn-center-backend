<?php

namespace App\Repositories;

use App\Enums\AppointmentStatusEnum;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Repositories\Contracts\BaseRepository;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Collection;

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
     * @param                        $appointmentSequence
     * @param string|Carbon|DateTime $date
     * @param array                  $data
     * @return bool|int
     */
    public function updatePreviousCheckinClinicAppointments($clinicId, $appointmentSequence, string|Carbon|DateTime $date, array $data): bool|int
    {
        return $this->globalQuery()
            ->where('clinic_id', $clinicId)
            ->where('date', $date)
            ->where('status', AppointmentStatusEnum::CHECKIN->value)
            ->where('appointment_sequence', '<', $appointmentSequence)
            ->chunk(5, function (Collection $appointments) use ($data) {
                foreach ($appointments as $appointment) {
                    $appointment->update($data);
                    AppointmentLog::create([
                        'cancellation_reason' => $data['cancellation_reason'] ?? "",
                        'status'              => $data['status'] ?? $appointment->status,
                        'happen_in'           => now(),
                        'appointment_id'      => $appointment->id,
                        'actor_id'            => auth()->user()?->id,
                        'affected_id'         => $data['customer_id'] ?? $appointment->customer_id,
                        'event'               => "appointment status has been changed to {$data['status']} in " . now()->format('Y-m-d H:i:s') . " By " . auth()->user()->full_name->en
                    ]);
                }
            });
    }
}
