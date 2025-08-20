<?php

namespace App\Services;

use App\Enums\AppointmentStatusEnum;
use App\Exceptions\Application\MoreThanOneAppointmentWithOneClinic;
use App\Models\Appointment;
use App\Repositories\AppointmentLogRepository;
use App\Repositories\AppointmentRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<Appointment>
 * @property AppointmentRepository $repository
 */
class AppointmentService extends BaseService
{
    use Makable;

    protected string $repositoryClass = AppointmentRepository::class;

    public function store(array $data, array $relationships = [], array $countable = []): ?Model
    {
        $data['appointment_sequence'] = $this->calculateAppointmentSequence($data['clinic_id'], $data['date_time']);

        if (!isset($data['status'])) {
            $data['status'] = AppointmentStatusEnum::PENDING->value;
        }

        $prevAppointments = $this->repository->getByCustomerAndClinicAndDate($data['customer_id'], $data['clinic_id'], Carbon::parse($data['date_time'])->format('Y-m-d'));;

        if ($prevAppointments->where('status', '!=', AppointmentStatusEnum::CANCELLED->value)->isNotEmpty()) {
            throw new MoreThanOneAppointmentWithOneClinic();
        }

        $appointment = $this->repository->create($data);
        $appointment->updateTotalCost();
        $this->logAppointment($data, $appointment);
        return $appointment->load($relationships)->loadCount($countable);
    }

    /**
     * Calculate appointment sequence number based on appointment time
     * @param int    $clinicId
     * @param string $dateTime
     * @return int
     */
    public function calculateAppointmentSequence(int $clinicId, string $dateTime): int
    {
        $appointmentDate = Carbon::parse($dateTime)->format('Y-m-d');
        $appointmentTime = Carbon::parse($dateTime);
        $existingAppointments = $this->repository->getClinicAppointmentsOrderedByTime($clinicId, $appointmentDate);

        if ($existingAppointments->isEmpty()) {
            return 1;
        }

        $insertPosition = -1;

        $allAppointmentTimes = [];
        foreach ($existingAppointments as $index => $appointment) {
            $allAppointmentTimes[] = [
                'time' => Carbon::parse($appointment->date_time),
                'id' => $appointment->id,
                'is_new' => false,
                'index' => $index
            ];
        }

        // Add the new appointment
        $allAppointmentTimes[] = [
            'time' => $appointmentTime,
            'id' => null,
            'is_new' => true,
            'index' => null
        ];

        usort($allAppointmentTimes, function ($a, $b) {
            return $a['time']->lt($b['time']) ? -1 : 1;
        });

        foreach ($allAppointmentTimes as $seqNum => $appt) {
            if ($appt['is_new']) {
                $insertPosition = $seqNum + 1;
            } else {
                $this->repository->update([
                    'appointment_sequence' => $seqNum + 1
                ], $existingAppointments[$appt['index']]);
            }
        }
        return $insertPosition;
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        $appointment = $this->repository->find($id);
        if (!$appointment) {
            return null;
        }

        if (isset($data['date_time']) && $data['date_time'] !== $appointment->date_time?->format('Y-m-d H:i')) {
            $this->removeFromSequence($appointment);
            $data['appointment_sequence'] = $this->calculateAppointmentSequence($appointment->clinic_id, $data['date_time']);
        }

        $prevAppointments = $this->repository
            ->getByCustomerAndClinicAndDate(
                $data['customer_id'] ?? $appointment->id,
                $data['clinic_id'] ?? $appointment->clinic_id,
                Carbon::parse($data['date_time'] ?? $appointment->date_time)->format('Y-m-d')
            );

        if ($prevAppointments->where('status', '!=', AppointmentStatusEnum::CANCELLED->value)->where('id', '!=', $appointment->id)->isNotEmpty()) {
            throw new MoreThanOneAppointmentWithOneClinic();
        }

        $appointment = $this->repository->update($data, $id, $relationships, $countable);

        $appointment->updateTotalCost();
        $this->logAppointment($data, $appointment, true);
        return $appointment->load($relationships)->loadCount($countable);
    }

    /**
     * Remove an appointment from the sequence by updating other appointments
     * @param Appointment $appointment
     * @return void
     */
    private function removeFromSequence(Appointment $appointment): void
    {
        $date = $appointment->date_time->format('Y-m-d');
        $sequence = $appointment->appointment_sequence;

        if (!$sequence) {
            return;
        }

        $allAppointments = $this->repository->getClinicAppointmentsOrderedByTime(
            $appointment->clinic_id,
            $date
        );

        $filteredAppointments = $allAppointments->filter(function (Appointment $appt) use ($appointment) {
            return $appt->id !== $appointment->id;
        });

        $filteredAppointments = $filteredAppointments->values();

        foreach ($filteredAppointments as $index => $appt) {
            $this->repository->update([
                'appointment_sequence' => $index + 1
            ], $appt);
        }
    }

    public function changeAppointmentStatus(array $data, array $relations = [], array $countable = [])
    {
        $appointment = $this->repository->find($data['appointment_id']);

        if (!$appointment) {
            return null;
        }

        $appointment = $this->repository->update($data, $appointment, $relations, $countable);
        $this->logAppointment($data, $appointment, true);
        return $appointment;
    }

    private function logAppointment(array $data, Appointment $appointment, bool $isUpdate = false): void
    {
        if (!$isUpdate) {
            AppointmentLogRepository::make()->create([
                'cancellation_reason' => $data['cancellation_reason'] ?? null,
                'status' => $data['status'] ?? $appointment->status,
                'happen_in' => now(),
                'appointment_id' => $appointment->id,
                'actor_id' => user()->id,
                'affected_id' => $data['customer_id'] ?? $appointment->customer_id,
                'event' => "appointment has been created in " . now()->format('Y-m-d H:i:s') . " By " . user()?->full_name,
            ]);
        } else {
            AppointmentLogRepository::make()->create([
                'cancellation_reason' => $data['cancellation_reason'] ?? null,
                'status' => $data['status'] ?? $appointment->status,
                'happen_in' => now(),
                'appointment_id' => $appointment->id,
                'actor_id' => user()?->id,
                'affected_id' => $data['customer_id'] ?? $appointment->customer_id,
                'event' => "appointment has been Updated in " . now()->format('Y-m-d H:i:s') . " By " . user()?->full_name,
            ]);
        }
    }

    public function paginateByClinic(int $clinicId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->paginateByClinic($clinicId, $relations, $countable);
    }

    public function paginateByCustomer(int $customerId, array $relations = [], array $countable = []): ?array
    {
        return $this->repository->paginateByCustomer($customerId, $relations, $countable);
    }

    public function view($id, array $relationships = [], array $countable = []): ?Appointment
    {
        $appointment = $this->repository->find($id, $relationships, $countable);
        if (!$appointment) {
            return null;
        }

        if ($appointment->customer_id != customer()?->id && isCustomer()) {
            return null;
        }

        return $appointment;
    }

    public function cancelAppointment(int $appointmentId)
    {
        $appointment = $this->repository->find($appointmentId);
        if (!$appointment) {
            return null;
        }

        if (isCustomer() && $appointment->customer_id != customer()?->id) {
            return null;
        }

        if ($appointment->status != AppointmentStatusEnum::PENDING->value) {
            return null;
        }

        return $this->repository->update([
            'status' => AppointmentStatusEnum::CANCELLED->value,
        ], $appointment);
    }
}
