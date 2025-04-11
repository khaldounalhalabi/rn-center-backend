<?php

namespace App\Managers;

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Repositories\AppointmentLogRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\PatientProfileRepository;
use App\Repositories\ServiceRepository;
use Illuminate\Support\Facades\Log;

class AppointmentManager
{
    private static ?AppointmentManager $instance = null;

    public function store(array $data, array $relationships = [], array $countable = []): ?Appointment
    {
        $clinic = ClinicRepository::make()->find($data['clinic_id']);
        if (!$clinic) {
            return null;
        }
        if (!$clinic->canHasAppointmentIn($data['date'])) {
            return null;
        }
        $data['appointment_sequence'] = $this->handleAppointmentSequence($data, $clinic) ?? null;
        $servicePrice = $this->getServiceCost($data);

        $data['total_cost'] = $this->calculateAppointmentTotalCost($data, $servicePrice, $clinic);

        $appointment = AppointmentRepository::make()->create($data);

        $this->logAppointment($data, $appointment);

        $patientProfile = PatientProfileRepository::make()->getByClinicAndCustomer($appointment->clinic_id, $appointment->customer_id);

        if (!$patientProfile) {
            PatientProfileRepository::make()->create([
                'clinic_id' => $clinic->id,
                'customer_id' => $appointment->customer_id,
            ]);
        }

        return $appointment->load($relationships)->loadCount($countable);
    }

    public static function make(): static
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        } elseif (!(self::$instance instanceof static)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    private function handleAppointmentSequence(array $data, Clinic $clinic): float|int|string|null
    {
        /** @var Appointment $lastAppointmentInDay */
        $lastAppointmentInDay = AppointmentRepository::make()->getClinicLastAppointmentInDay($clinic->id, $data['date']);
        if ($lastAppointmentInDay) {
            return $lastAppointmentInDay->appointment_sequence + 1;
        } else {
            return 1;
        }
    }

    private function getServiceCost(array $data)
    {
        if (isset($data['service_id'])) {
            $service = ServiceRepository::make()->find($data['service_id']);
            if (!$service) {
                return 0;
            }

            return $service->price;
        } else {
            return 0;
        }
    }

    private function calculateAppointmentTotalCost(array $data, $servicePrice, Clinic $clinic, ?Appointment $appointment = null)
    {
        if ((isset($data['is_revision']) && $data['is_revision']) || $appointment?->is_revision) {
            $appointmentCost = 0;
            $servicePrice = 0;
        } else {
            $appointmentCost = $clinic->appointment_cost;
        }

        return $servicePrice
            + ($data['extra_fees'] ?? ($appointment?->extra_fees ?? 0))
            + ($appointmentCost)
            - ($data['discount'] ?? ($appointment?->discount ?? 0));
    }

    private function logAppointment(array $data, Appointment $appointment, bool $isUpdate = false): void
    {
        if (!$isUpdate) {
            AppointmentLogRepository::make()->create([
                'cancellation_reason' => $data['cancellation_reason'] ?? null,
                'status' => $data['status'],
                'happen_in' => now(),
                'appointment_id' => $appointment->id,
                'actor_id' => auth()->user()->id,
                'affected_id' => $data['customer_id'] ?? $appointment->customer_id,
                'event' => "appointment has been created in " . now()->format('Y-m-d H:i:s') . " By " . auth()->user()?->full_name,
            ]);
        } else {
            AppointmentLogRepository::make()->create([
                'cancellation_reason' => $data['cancellation_reason'] ?? null,
                'status' => $data['status'],
                'happen_in' => now(),
                'appointment_id' => $appointment->id,
                'actor_id' => auth()->user()?->id,
                'affected_id' => $data['customer_id'] ?? $appointment->customer_id,
                'event' => "appointment has been Updated in " . now()->format('Y-m-d H:i:s') . " By " . auth()->user()?->full_name,
            ]);
        }
    }

    public function update(array $data, $id, array $relationships = [], array $countable = [])
    {
        $appointment = AppointmentRepository::make()->find($id);

        if (!$appointment?->canUpdate()) {
            return null;
        }

        if (
            $appointment->status == AppointmentStatusEnum::CHECKOUT->value
            && $appointment->type == AppointmentTypeEnum::ONLINE->value
        ) {
            return null;
        }

        if (isset($data['status'])
            && $data['status'] != $appointment->status
            && auth()->user()?->isClinic()
            && !$this->canUpdateOnlineAppointmentStatus($appointment, $data['status'])) {
            return null;
        }

        $clinic = $appointment->clinic;
        $this->logAppointment($data, $appointment, true);
        $servicePrice = $this->getServiceCost($data);
        $data['total_cost'] = $this->calculateAppointmentTotalCost($data, $servicePrice, $clinic, $appointment);
        $appointment = AppointmentRepository::make()->update($data, $appointment);

        return $appointment->load($relationships)->loadCount($countable);
    }

    public function canUpdateOnlineAppointmentStatus(Appointment $appointment, ?string $status = null): ?bool
    {
        if (!$status) {
            return true;
        }

        if ($appointment->type != AppointmentTypeEnum::ONLINE->value) {
            return true;
        }

        if ($status != AppointmentStatusEnum::CHECKOUT->value
            && $appointment->status == AppointmentStatusEnum::CHECKOUT->value) {
            return false;
        }

        if ($status == AppointmentStatusEnum::CANCELLED->value
            && $appointment->status != AppointmentStatusEnum::CHECKOUT->value) {
            return true;
        }

        if (
            $appointment->status == AppointmentStatusEnum::PENDING->value
            && !in_array($status, [
                AppointmentStatusEnum::PENDING->value,
                AppointmentStatusEnum::BOOKED->value,
                AppointmentStatusEnum::CANCELLED->value,
            ])
        ) {
            return false;
        }

        if (
            $appointment->status == AppointmentStatusEnum::BOOKED->value
            && !in_array($status, AppointmentStatusEnum::getAllValues([
                AppointmentStatusEnum::PENDING,
                AppointmentStatusEnum::CANCELLED->value,
            ]))
        ) {
            return false;
        }
        if ($appointment->status == AppointmentStatusEnum::CHECKIN->value
            && !in_array($status, AppointmentStatusEnum::getAllValues([
                AppointmentStatusEnum::PENDING, AppointmentStatusEnum::BOOKED,
                AppointmentStatusEnum::CANCELLED->value,
            ]))) {
            return false;
        }

        if ($appointment->status == AppointmentStatusEnum::CHECKOUT->value
            && !in_array($status, [
                AppointmentStatusEnum::CHECKOUT->value, AppointmentStatusEnum::CANCELLED->value,
            ])) {
            return false;
        }

        if ($appointment->status == AppointmentStatusEnum::CANCELLED->value
            && $status != AppointmentStatusEnum::CANCELLED->value) {
            return false;
        }

        return true;
    }
}
