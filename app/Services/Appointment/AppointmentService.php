<?php

namespace App\Services\Appointment;

use App\Enums\AppointmentStatusEnum;
use App\Jobs\UpdateAppointmentRemainingTimeJob;
use App\Models\Appointment;
use App\Notifications\AppointmentStatusChangedNotification;
use App\Repositories\AppointmentLogRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\ServiceRepository;
use App\Services\Contracts\BaseService;
use App\Services\Notification\FirebaseServices;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements IAppointmentService<Appointment>
 * @extends BaseService<Appointment , BaseRepository>
 */
class AppointmentService extends BaseService implements IAppointmentService
{
    private ClinicRepository $clinicRepository;
    private ServiceRepository $serviceRepository;
    private AppointmentLogRepository $appointmentLogRepository;

    /**
     * AppointmentService constructor.
     * @param AppointmentRepository    $repository
     * @param ClinicRepository         $clinicRepository
     * @param ServiceRepository        $serviceRepository
     * @param AppointmentLogRepository $appointmentLogRepository
     */
    public function __construct(AppointmentRepository    $repository,
                                ClinicRepository         $clinicRepository,
                                ServiceRepository        $serviceRepository,
                                AppointmentLogRepository $appointmentLogRepository)
    {
        parent::__construct($repository);
        $this->clinicRepository = $clinicRepository;
        $this->serviceRepository = $serviceRepository;
        $this->appointmentLogRepository = $appointmentLogRepository;
    }

    /**
     * @param array $data
     * @param array $relationships
     * @param array $countable
     * @return Appointment|null
     */
    public function store(array $data, array $relationships = [], array $countable = []): ?Appointment
    {
        $clinic = $this->clinicRepository->find($data['clinic_id']);

        if (!$clinic) {
            return null;
        }

        if (!$clinic->canHasAppointmentIn($data['date'], $data['customer_id'])) {
            return null;
        }

        if (!in_array($data['status'], [
            AppointmentStatusEnum::CANCELLED->value,
            AppointmentStatusEnum::PENDING->value
        ])) {
            /** @var Appointment $lastAppointmentInDay */
            $lastAppointmentInDay = $this->repository->getLastAppointmentInDay($data['date']);
            if ($lastAppointmentInDay) {
                $data['appointment_sequence'] = $lastAppointmentInDay->appointment_sequence + 1;
            } else {
                $data['appointment_sequence'] = 1;
            }
        }

        if (isset($data['service_id'])) {
            $service = $this->serviceRepository->find($data['service_id']);
            if (!$service) {
                return null;
            }

            $data['total_cost'] = $service->price + ($data['extra_fees'] ?? 0);
        } else {
            $data['total_cost'] = $clinic->appointment_cost + ($data['extra_fees'] ?? 0);
        }

        $appointment = $this->repository->create($data, $relationships, $countable);

        $this->appointmentLogRepository->create([
            'cancellation_reason' => $data['cancellation_reason'] ?? null,
            'status' => $data['status'],
            'happen_in' => now(),
            'appointment_id' => $appointment->id,
            'actor_id' => auth()->user()->id,
            'affected_id' => $data['customer_id'] ?? $appointment->customer_id,
            'event' => "appointment has been created in " . now()->format('Y-m-d H:i:s') . " By " . auth()->user()->full_name->en
        ]);

        return $appointment;
    }

    public function update(array $data, $id, array $relationships = [], array $countable = []): ?Model
    {
        /** @var Appointment $appointment */
        $appointment = $this->repository->find($id);

        if (!$appointment) {
            return null;
        }

        if (!$appointment->canUpdate()) {
            return null;
        }

        $clinic = $appointment->clinic;

        if (!$clinic->canHasAppointmentIn(
            $data['date'] ?? $appointment->date->format('Y-m-d'),
            $data['customer_id'] ?? $appointment->customer_id
        )) {
            return null;
        }

        if (
            isset($data['date'])
            && $data['date'] != $appointment->date
            && !in_array($data['status'], [
                AppointmentStatusEnum::CANCELLED->value,
                AppointmentStatusEnum::PENDING->value
            ])) {
            /** @var Appointment $appointment */
            $lastAppointmentInDay = $this->repository->getLastAppointmentInDay($data['date']);
            if ($lastAppointmentInDay) {
                $data['appointment_sequence'] = $lastAppointmentInDay->appointment_sequence + 1;
            } else {
                $data['appointment_sequence'] = 1;
            }
        }

        $this->appointmentLogRepository->create([
            'cancellation_reason' => $data['cancellation_reason'] ?? null,
            'status' => $data['status'],
            'happen_in' => now(),
            'appointment_id' => $appointment->id,
            'actor_id' => auth()->user()->id,
            'affected_id' => $data['customer_id'] ?? $appointment->customer_id,
            'event' => "appointment has been Updated in " . now()->format('Y-m-d H:i:s') . " By " . auth()->user()->full_name->en
        ]);

        if (isset($data['service_id'])) {
            $service = $this->serviceRepository->find($data['service_id']);
            if (!$service) {
                return null;
            }
            $data['total_cost'] = $service->price + ($data['extra_fees'] ?? 0);
        } else {
            $data['total_cost'] = $clinic->appointment_cost + ($data['extra_fees'] ?? 0);
        }

        $prevStatus = $appointment->status;

        $appointment = $this->repository->update($data, $appointment, $relationships, $countable);

        if ($appointment->status == AppointmentStatusEnum::CHECKOUT->value && $prevStatus != AppointmentStatusEnum::CHECKOUT->value) {
            UpdateAppointmentRemainingTimeJob::dispatch($appointment->clinic_id, $appointment->date);
        }

        if ($appointment->status == AppointmentStatusEnum::BOOKED->value && $prevStatus != AppointmentStatusEnum::BOOKED->value) {
            $appointment = Appointment::handleRemainingTime($appointment);
            $appointment->save();
        }

        return $appointment;
    }

    /**
     * @param       $clinicId
     * @param array $relations
     * @param int   $perPage
     * @return null|array
     */
    public function getClinicAppointments($clinicId, array $relations = [], int $perPage = 10): ?array
    {
        return $this->repository->getByClinic($clinicId, $relations, $perPage);
    }

    /**
     * @param       $appointmentId
     * @param array $data
     * @return Appointment|null
     */
    public function toggleAppointmentStatus($appointmentId, array $data): ?Appointment
    {
        $appointment = $this->repository->find($appointmentId);

        if (!$appointment) {
            return null;
        }

        if ($data['status'] == AppointmentStatusEnum::CANCELLED->value && !isset($data['cancellation_reason'])) {
            return null;
        }

        $prevStatus = $appointment->status;

        $appointment = $this->repository->update([
            'status' => $data['status'],
            'cancellation_reason' => $data['cancellation_reason'] ?? ""
        ], $appointment);

        if ($appointment->status == AppointmentStatusEnum::CHECKOUT->value && $prevStatus != AppointmentStatusEnum::CHECKOUT->value) {
            UpdateAppointmentRemainingTimeJob::dispatch($appointment->clinic_id, $appointment->date);
        }

        if ($data['status'] == AppointmentStatusEnum::BOOKED->value && $prevStatus != AppointmentStatusEnum::BOOKED->value) {
            $appointment = Appointment::handleRemainingTime($appointment);
            $appointment->save();
        }

        FirebaseServices::make()
            ->setData([
                'appointment' => $appointment
            ])
            ->setMethod('one')
            ->setTo(auth()->user())
            ->setNotification(AppointmentStatusChangedNotification::class)
            ->send();

        $this->appointmentLogRepository->create([
            'cancellation_reason' => $data['cancellation_reason'] ?? "",
            'status' => $data['status'],
            'happen_in' => now(),
            'appointment_id' => $appointment->id,
            'actor_id' => auth()->user()->id,
            'affected_id' => $data['customer_id'] ?? $appointment->customer_id,
            'event' => "appointment status has been changed to {$data['status']} in " . now()->format('Y-m-d H:i:s') . " By " . auth()->user()->full_name->en
        ]);

        return $appointment;
    }
}
