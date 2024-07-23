<?php

namespace App\Services;

use App\Enums\AppointmentStatusEnum;
use App\Managers\AppointmentManager;
use App\Models\Appointment;
use App\Repositories\AppointmentLogRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\CustomerRepository;
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

    /**
     * @param array $data
     * @param array $relationships
     * @param array $countable
     * @return Appointment|null
     */
    public function store(array $data, array $relationships = [], array $countable = []): ?Appointment
    {
        return AppointmentManager::make()->store($data, $relationships, $countable);
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
        if (!$appointment?->canUpdate()) {
            return null;
        }

        $oldStatus = $appointment->status;

        if ($data['status'] == AppointmentStatusEnum::CANCELLED->value && !isset($data['cancellation_reason'])) {
            return null;
        }

        $appointmentManager = AppointmentManager::make();

        if (auth()->user()?->isClinic()
            && !$appointmentManager->canUpdateOnlineAppointmentStatus($appointment, $data['status'])) {
            return null;
        }

        $prevStatus = $appointment->status;

        $appointment = $this->repository->update([
            'status'              => $data['status'],
            'cancellation_reason' => $data['cancellation_reason'] ?? ""
        ], $appointment, ['customer.user', 'clinic.user']);

        $appointmentManager->checkoutPreviousAppointmentsIfNewStatusIsCheckin($appointment, $prevStatus);
        $appointmentManager->handleAppointmentRemainingTime($appointment, $prevStatus);
        $appointmentManager->handleChangeAppointmentNotifications($appointment, $oldStatus);
        $appointmentManager->handleTransactionsWhenChangeStatus($appointment, $prevStatus);

        AppointmentLogRepository::make()->create([
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
        return AppointmentManager::make()->update($data, $id, $relationships, $countable);
    }

    /**
     * @param int      $customerId
     * @param int|null $clinicId
     * @param array    $relations
     * @param array    $countable
     * @return Appointment|null
     */
    public function getCustomerLastAppointment(int $customerId, ?int $clinicId = null, array $relations = [], array $countable = []): ?Appointment
    {
        return $this->repository->getCustomerLastAppointment($customerId, $clinicId, $relations, $countable);
    }

    /**
     * @param       $id
     * @param       $date
     * @param array $relationships
     * @param array $countable
     * @return Appointment|null
     */
    public function updateAppointmentDate($id, $date, array $relationships = [], array $countable = []): ?Appointment
    {
        $data['date'] = $date;

        /** @var Appointment $appointment */
        $appointment = $this->repository->find($id);

        if (!$appointment?->canUpdate()) {
            return null;
        }

        $clinic = $appointment->clinic;

        if (!$clinic->canHasAppointmentIn(
            $data['date'],
            $appointment->customer_id
        )) {
            return null;
        }

        if (
            isset($data['date'])
            && $data['date'] != $appointment->date
            && $appointment->status == AppointmentStatusEnum::BOOKED->value
        ) {
            /** @var Appointment $appointment */
            $lastAppointmentInDay = $this->repository->getClinicLastAppointmentInDay($clinic->id, $data['date']);
            if ($lastAppointmentInDay) {
                $data['appointment_sequence'] = $lastAppointmentInDay->appointment_sequence + 1;
            } else {
                $data['appointment_sequence'] = 1;
            }
        }
        $appointment = $this->repository->update($data, $appointment, $relationships, $countable);

        AppointmentLogRepository::make()->create([
            'status'         => $appointment->status,
            'happen_in'      => now(),
            'appointment_id' => $appointment->id,
            'actor_id'       => auth()->user()->id,
            'affected_id'    => $appointment->customer_id,
            'event'          => "appointment has been Updated in " . now()->format('Y-m-d H:i:s') . " By " . auth()->user()->full_name->en
        ]);
        AppointmentManager::make()->handleChangeAppointmentNotifications($appointment);
        return $appointment;
    }

    public function view($id, array $relationships = [], array $countable = []): ?Model
    {
        $appointment = $this->repository->find($id);

        if (!$appointment?->canShow()) {
            return null;
        }

        return $appointment->load($relationships)->loadCount($countable);
    }

    public function getCustomerTodayAppointments(array $relations = [], array $countable = [])
    {
        return $this->repository->getByDate(
            now()->format('Y-m-d'),
            auth()->user()?->customer?->id,
            null,
            $relations,
            $countable
        );
    }

    public function getByCustomer($customerId, array $relations = [], array $countable = []): ?array
    {
        $customer = CustomerRepository::make()->find($customerId);
        if (!$customer?->canShow()) {
            return null;
        }

        return $this->repository->getByCustomer($customerId, $relations, $countable);
    }
}
