<?php

namespace App\Services\Appointment;

use App\Models\Appointment;
use App\Repositories\AppointmentLogRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\Contracts\BaseRepository;
use App\Repositories\ServiceRepository;
use App\Services\Contracts\BaseService;
use App\Repositories\AppointmentRepository;
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
     *
     * @param AppointmentRepository $repository
     * @param ClinicRepository $clinicRepository
     * @param ServiceRepository $serviceRepository
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

        if (!$clinic->canHasAppointmentIn($data['date'], $data['from'], $data['to'], $data['customer_id'])) {
            return null;
        }

        /** @var Appointment $lastAppointmentInDay */
        $lastAppointmentInDay = $this->repository->getLastAppointmentInDay($data['date']);
        if ($lastAppointmentInDay) {
            $data['appointment_sequence'] = $lastAppointmentInDay->appointment_sequence + 1;
        } else {
            $data['appointment_sequence'] = 1;
        }

        if (isset($data['service_id'])) {
            $service = $this->serviceRepository->find($data['service_id']);
            if (!$service) {
                return null;
            }

            $data['total_cost'] = $service->price + ($data['extra_fees'] ?? 0);
        } else {
            return null;
        }

        $appointment =  $this->repository->create($data, $relationships, $countable);

        $this->appointmentLogRepository->create([
            'cancellation_reason' => $data['cancellation_reason'] ?? null,
            'status' => $data['status'],
            'happen_in' => now(),
            'appointment_id' => $appointment->id,
            'actor_id' => auth()->user()->id,
            'affected_id' => $data['customer_id'] ?? $appointment->customer_id
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
            $data['from'] ?? $appointment->from->format('H:i'),
            $data['to'] ?? $appointment->to->format('H:i'),
            $appointment->customer_id)) {
            return null;
        }

        if (isset($data['date']) && $data['date'] != $appointment->date) {
            /** @var Appointment $appointment */
            $lastAppointmentInDay = $this->repository->getLastAppointmentInDay($data['date']);
            if ($lastAppointmentInDay) {
                $data['appointment_sequence'] = $lastAppointmentInDay->appointment_sequence + 1;
            } else {
                $data['appointment_sequence'] = 1;
            }
        }

        if ($data['status'] != $appointment->status) {
            $this->appointmentLogRepository->create([
                'cancellation_reason' => $data['cancellation_reason'] ?? null,
                'status' => $data['status'],
                'happen_in' => now(),
                'appointment_id' => $appointment->id,
                'actor_id' => auth()->user()->id,
                'affected_id' => $data['customer_id'] ?? $appointment->customer_id
            ]);
        }

        if (isset($data['service_id'])) {
            $service = $this->serviceRepository->find($data['service_id']);
            if (!$service) {
                return null;
            }

            $data['total_cost'] = $service->price + ($data['extra_fees'] ?? 0);
        } else {
            $data['total_cost'] = $appointment->service->price + ($data['extra_fees'] ?? $appointment->extra_fees);
        }

        return $this->repository->update($data, $appointment, $relationships, $countable);
    }

    /**
     * @param $clinicId
     * @param array $relations
     * @param int $perPage
     * @return null|array
     */
    public function getClinicAppointments($clinicId, array $relations = [], int $perPage = 10): ?array
    {
        return $this->repository->getByClinic(auth()->user()?->id, $relations, $perPage);
    }
}
