<?php

namespace App\Services;

use App\Enums\AppointmentStatusEnum;
use App\Enums\RolesPermissionEnum;
use App\Jobs\UpdateAppointmentRemainingTimeJob;
use App\Models\Appointment;
use App\Notifications\Customer\CustomerAppointmentChangedNotification;
use App\Notifications\RealTime\AppointmentStatusChangeNotification;
use App\Repositories\AppointmentLogRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\ServiceRepository;
use App\Services\Contracts\BaseService;
use App\Traits\Makable;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseService<Appointment , BaseRepository>
 * @property AppointmentRepository $repository
 */
class AppointmentService extends BaseService
{
    use Makable;

    protected string $repositoryClass = AppointmentRepository::class;

    private ClinicRepository $clinicRepository;
    private ServiceRepository $serviceRepository;
    private AppointmentLogRepository $appointmentLogRepository;


    public function init(): void
    {
        parent::__construct();
        $this->clinicRepository = ClinicRepository::make();
        $this->serviceRepository = ServiceRepository::make();
        $this->appointmentLogRepository = AppointmentLogRepository::make();
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
            $lastAppointmentInDay = $this->repository->getClinicLastAppointmentInDay($clinic->id, $data['date']);
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
        }

        $data['total_cost'] = ($service?->price ?? 0) + ($data['extra_fees'] ?? 0) + $clinic->appointment_cost - ($data['discount'] ?? 0);

        $appointment = $this->repository->create($data, $relationships, $countable);

        $this->appointmentLogRepository->create([
            'cancellation_reason' => $data['cancellation_reason'] ?? null,
            'status'              => $data['status'],
            'happen_in'           => now(),
            'appointment_id'      => $appointment->id,
            'actor_id'            => auth()->user()->id,
            'affected_id'         => $data['customer_id'] ?? $appointment->customer_id,
            'event'               => "appointment has been created in " . now()->format('Y-m-d H:i:s') . " By " . auth()->user()->full_name->en
        ]);

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
        $appointment = $this->repository->find($appointmentId, ['customer.user', 'clinic.user']);

        if (!$appointment) {
            return null;
        }

        $oldStatus = $appointment->status;

        if ($data['status'] == AppointmentStatusEnum::CANCELLED->value && !isset($data['cancellation_reason'])) {
            return null;
        }

        $prevStatus = $appointment->status;

        $appointment = $this->repository->update([
            'status'              => $data['status'],
            'cancellation_reason' => $data['cancellation_reason'] ?? ""
        ], $appointment, ['customer.user', 'clinic.user']);

        if (
            $appointment->status == AppointmentStatusEnum::CHECKIN->value
            && $prevStatus != AppointmentStatusEnum::CHECKIN->value
        ) {
            $this->repository->updatePreviousCheckinClinicAppointments($appointment->clinic_id,
                $appointment->appointment_sequence,
                $appointment->date,
                [
                    'status' => AppointmentStatusEnum::CHECKOUT->value
                ]);
        }

        if ($appointment->status == AppointmentStatusEnum::CHECKOUT->value && $prevStatus != AppointmentStatusEnum::CHECKOUT->value) {
            UpdateAppointmentRemainingTimeJob::dispatch($appointment->clinic_id, $appointment->date);
        }

        if ($data['status'] == AppointmentStatusEnum::BOOKED->value && $prevStatus != AppointmentStatusEnum::BOOKED->value) {
            $appointment = Appointment::handleRemainingTime($appointment);
            $appointment->save();
        }

        $this->handleChangeAppointmentNotifications($appointment, $oldStatus);

        $this->appointmentLogRepository->create([
            'cancellation_reason' => $data['cancellation_reason'] ?? "",
            'status'              => $data['status'],
            'happen_in'           => now(),
            'appointment_id'      => $appointment->id,
            'actor_id'            => auth()->user()->id,
            'affected_id'         => $data['customer_id'] ?? $appointment->customer_id,
            'event'               => "appointment status has been changed to {$data['status']} in " . now()->format('Y-m-d H:i:s') . " By " . auth()->user()->full_name->en
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

        $oldStatus = $appointment->status;

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
            && $data['status'] == AppointmentStatusEnum::BOOKED->value
        ) {
            /** @var Appointment $appointment */
            $lastAppointmentInDay = $this->repository->getClinicLastAppointmentInDay($clinic->id, $data['date']);
            if ($lastAppointmentInDay) {
                $data['appointment_sequence'] = $lastAppointmentInDay->appointment_sequence + 1;
            } else {
                $data['appointment_sequence'] = 1;
            }
        }

        $this->appointmentLogRepository->create([
            'cancellation_reason' => $data['cancellation_reason'] ?? null,
            'status'              => $data['status'],
            'happen_in'           => now(),
            'appointment_id'      => $appointment->id,
            'actor_id'            => auth()->user()->id,
            'affected_id'         => $data['customer_id'] ?? $appointment->customer_id,
            'event'               => "appointment has been Updated in " . now()->format('Y-m-d H:i:s') . " By " . auth()->user()->full_name->en
        ]);

        if (isset($data['service_id'])) {
            $service = $this->serviceRepository->find($data['service_id']);
            if (!$service) {
                return null;
            }
        } else {
            $service = $appointment->service ?? null;
        }

        $data['total_cost'] = ($service?->price ?? 0)
            + ($data['extra_fees'] ?? ($appointment->extra_fees ?? 0))
            + $clinic->appointment_cost
            - ($data['discount'] ?? ($appointment->discount ?? 0));

        $prevStatus = $appointment->status;

        $appointment = $this->repository->update($data, $appointment, $relationships, $countable);

        if (
            $appointment->status == AppointmentStatusEnum::CHECKOUT->value
            && $prevStatus != AppointmentStatusEnum::CHECKOUT->value
        ) {
            UpdateAppointmentRemainingTimeJob::dispatch($appointment->clinic_id, $appointment->date);
        }

        if (
            $appointment->status == AppointmentStatusEnum::BOOKED->value
            && $prevStatus != AppointmentStatusEnum::BOOKED->value
        ) {
            $appointment = Appointment::handleRemainingTime($appointment);
            $appointment->save();
        }

        if (
            $appointment->status == AppointmentStatusEnum::CHECKIN->value
            && $prevStatus != AppointmentStatusEnum::CHECKIN->value
        ) {
            $this->repository->updatePreviousCheckinClinicAppointments($clinic->id,
                $appointment->appointment_sequence,
                $appointment->date,
                [
                    'status' => AppointmentStatusEnum::CHECKOUT->value
                ]);
        }

        $this->handleChangeAppointmentNotifications($appointment, $oldStatus);

        return $appointment;
    }

    /**
     * @param mixed  $appointment
     * @param string $oldStatus
     * @return void
     */
    private function handleChangeAppointmentNotifications(mixed $appointment, string $oldStatus): void
    {
        if ($oldStatus != $appointment->status) {
            FirebaseServices::make()
                ->setData([
                    'appointment' => $appointment
                ])
                ->setMethod(FirebaseServices::ONE)
                ->setTo($appointment->customer->user)
                ->setNotification(CustomerAppointmentChangedNotification::class)
                ->send();

            FirebaseServices::make()
                ->setData([
                    'appointment' => $appointment,
                ])
                ->setMethod(FirebaseServices::ByRole)
                ->setRole(RolesPermissionEnum::ADMIN['role'])
                ->setNotification(AppointmentStatusChangeNotification::class)
                ->send();

            FirebaseServices::make()
                ->setData([
                    'appointment' => $appointment,
                ])
                ->setMethod(FirebaseServices::ONE)
                ->setTo($appointment->clinic->user)
                ->setNotification(AppointmentStatusChangeNotification::class)
                ->send();
        }
    }

    public function getCustomerLastAppointment(int $customerId, ?int $clinicId = null, array $relations = [], array $countable = []): ?Appointment
    {
        return $this->repository->getCustomerLastAppointment($customerId, $clinicId, $relations, $countable);
    }
}
