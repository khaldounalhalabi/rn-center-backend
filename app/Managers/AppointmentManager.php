<?php

namespace App\Managers;

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use App\Enums\OfferTypeEnum;
use App\Enums\RolesPermissionEnum;
use App\Jobs\UpdateAppointmentRemainingTimeJob;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Offer;
use App\Models\SystemOffer;
use App\Notifications\Customer\CustomerAppointmentChangedNotification;
use App\Notifications\RealTime\AppointmentStatusChangeNotification;
use App\Repositories\AppointmentLogRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\OfferRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\SystemOfferRepository;
use App\Services\FirebaseServices;

class AppointmentManager
{
    private static $instance;

    public static function make(): static
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        } elseif (!(self::$instance instanceof static)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * @param array $data
     * @param array $relationships
     * @param array $countable
     * @return Appointment|null
     */
    public function store(array $data, array $relationships = [], array $countable = []): ?Appointment
    {
        $clinic = ClinicRepository::make()->find($data['clinic_id']);
        if (!$clinic) {
            return null;
        }
        if (!$clinic->canHasAppointmentIn($data['date'], $data['customer_id'])) {
            return null;
        }
        $data['appointment_sequence'] = $this->handleAppointmentSequence($data, $clinic) ?? null;
        $servicePrice = $this->getServiceCost($data);
        [$clinicOffersTotal, $clinicOffersIds, $systemOffersTotal, $systemOffersIds] = $this->handleAppointmentOffers($data, $clinic->appointment_cost);
        $data['total_cost'] = $this->calculateAppointmentTotalCost($data, $servicePrice, $systemOffersTotal, $clinicOffersTotal, $clinic);

        $appointment = AppointmentRepository::make()->create($data);
        $appointment->systemOffers()->sync($systemOffersIds);
        $appointment->offers()->sync($clinicOffersIds);
        $this->logAppointment($data, $appointment);
        return $appointment->load($relationships)->loadCount($countable);
    }


    public function update(array $data, $id, array $relationships = [], array $countable = [])
    {
        $appointment = AppointmentRepository::make()->find($id);
        if (!$appointment) {
            return null;
        }
        $oldStatus = $appointment->status;
        if (!$appointment->canUpdate()) {
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
        $servicePrice = $this->getServiceCost($data, $appointment);
        [$clinicOffersTotal, $clinicOffersIds, $systemOffersTotal, $systemOffersIds] = $this->handleAppointmentOffers($data, $clinic->appointment_cost);
        $data['total_cost'] = $this->calculateAppointmentTotalCost($data, $servicePrice, $systemOffersTotal, $clinicOffersTotal, $clinic, $appointment);
        $prevStatus = $appointment->status;
        $appointment = AppointmentRepository::make()->update($data, $appointment);
        $appointment->systemOffers()->sync($systemOffersIds);
        $appointment->offers()->sync($clinicOffersIds);
        $this->handleAppointmentRemainingTime($appointment, $prevStatus);
        $this->checkoutPreviousAppointmentsIfNewStatusIsCheckin($appointment, $prevStatus);
        $this->handleChangeAppointmentNotifications($appointment, $oldStatus);

        return $appointment->load($relationships)->loadCount($countable);
    }

    /**
     * @param array  $data
     * @param Clinic $clinic
     * @return float|int|string|null
     */
    private function handleAppointmentSequence(array $data, Clinic $clinic): float|int|string|null
    {
        if (!in_array($data['status'], [
            AppointmentStatusEnum::CANCELLED->value,
            AppointmentStatusEnum::PENDING->value
        ])) {
            /** @var Appointment $lastAppointmentInDay */
            $lastAppointmentInDay = AppointmentRepository::make()->getClinicLastAppointmentInDay($clinic->id, $data['date']);
            if ($lastAppointmentInDay) {
                return $lastAppointmentInDay->appointment_sequence + 1;
            } else {
                return 1;
            }
        }
        return null;
    }

    private function getServiceCost(array $data, ?Appointment $appointment = null)
    {
        if (isset($data['service_id'])) {
            $service = ServiceRepository::make()->find($data['service_id']);
            if (!$service) {
                return 0;
            }

            return $service->price;
        } elseif ($appointment) {
            return $appointment->service?->price ?? 0;
        } else {
            return 0;
        }
    }

    /**
     * @param array $data
     * @param       $appointmentCost
     * @return array
     */
    private function handleAppointmentOffers(array $data, $appointmentCost): array
    {
        $clinicOffersTotal = 0;
        $clinicOffersIds = [];
        if (isset($data['offers'])) {
            $clinicOffers = OfferRepository::make()
                ->getByIds($data['offers'], $data['clinic_id']);
            $clinicOffersTotal = $clinicOffers
                ->sum(fn(Offer $offer) => $offer->type == OfferTypeEnum::FIXED->value
                    ? $offer->value
                    : ($offer->value * $appointmentCost) / 100
                );
            $clinicOffersIds = $clinicOffers->pluck('id')->toArray();
        }

        $systemOffersTotal = 0;
        $systemOffersIds = [];
        if (isset($data['system_offers'])) {
            $systemOffers = SystemOfferRepository::make()
                ->getByIds($data['system_offers'], $data['clinic_id']);
            $systemOffersTotal = $systemOffers
                ->sum(fn(SystemOffer $offer) => $offer->type == OfferTypeEnum::FIXED->value
                    ? $offer->amount
                    : ($offer->amount * $appointmentCost) / 100
                );
            $systemOffersIds = $systemOffers->pluck('id')->toArray();
        }
        return [
            $clinicOffersTotal,
            $clinicOffersIds,
            $systemOffersTotal,
            $systemOffersIds
        ];
    }

    private function calculateAppointmentTotalCost(array $data, $servicePrice, $systemOffersTotal, $clinicOffersTotal, Clinic $clinic, ?Appointment $appointment = null)
    {
        return $servicePrice
            + ($data['extra_fees'] ?? ($appointment?->extra_fees ?? 0))
            + ($clinic->appointment_cost - $systemOffersTotal - $clinicOffersTotal)
            - ($data['discount'] ?? ($appointment?->discount ?? 0));
    }

    /**
     * @param Appointment $appointment
     * @param string|null $status
     * @return bool|null
     */
    public function canUpdateOnlineAppointmentStatus(Appointment $appointment, ?string $status = null): ?bool
    {
        if (!$status) {
            return true;
        }

        if ($appointment->type != AppointmentTypeEnum::ONLINE->value) {
            return true;
        }

        if (
            $appointment->status == AppointmentStatusEnum::PENDING->value
            && !in_array($status, [
                AppointmentStatusEnum::PENDING->value,
                AppointmentStatusEnum::BOOKED->value
            ])
        ) {
            return false;
        }

        if (
            $appointment->status == AppointmentStatusEnum::BOOKED->value
            && !in_array($status, AppointmentStatusEnum::getAllValues([
                AppointmentStatusEnum::PENDING
            ]))
        ) {
            return false;
        }

        if ($appointment->status == AppointmentStatusEnum::CHECKIN->value
            && !in_array($status, AppointmentStatusEnum::getAllValues([
                AppointmentStatusEnum::PENDING, AppointmentStatusEnum::BOOKED
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

    /**
     * @param mixed $appointment
     * @param mixed $prevStatus
     * @return void
     */
    public function handleAppointmentRemainingTime(Appointment $appointment, mixed $prevStatus): void
    {
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
    }

    /**
     * @param mixed $appointment
     * @param mixed $prevStatus
     * @return void
     */
    public function checkoutPreviousAppointmentsIfNewStatusIsCheckin(mixed $appointment, mixed $prevStatus): void
    {
        if (
            $appointment->status == AppointmentStatusEnum::CHECKIN->value
            && $prevStatus != AppointmentStatusEnum::CHECKIN->value
        ) {
            AppointmentRepository::make()->updatePreviousCheckinClinicAppointments($appointment->clinic_id,
                $appointment->appointment_sequence,
                $appointment->date,
                [
                    'status' => AppointmentStatusEnum::CHECKOUT->value
                ]);
        }
    }

    /**
     * @param mixed  $appointment
     * @param string $oldStatus
     * @return void
     */
    public function handleChangeAppointmentNotifications(mixed $appointment, string $oldStatus): void
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

    private function logAppointment(array $data, Appointment $appointment, bool $isUpdate = false): void
    {
        if (!$isUpdate) {
            AppointmentLogRepository::make()->create([
                'cancellation_reason' => $data['cancellation_reason'] ?? null,
                'status'              => $data['status'],
                'happen_in'           => now(),
                'appointment_id'      => $appointment->id,
                'actor_id'            => auth()->user()->id,
                'affected_id'         => $data['customer_id'] ?? $appointment->customer_id,
                'event'               => "appointment has been created in " . now()->format('Y-m-d H:i:s') . " By " . auth()->user()->full_name->en
            ]);
        } else {
            AppointmentLogRepository::make()->create([
                'cancellation_reason' => $data['cancellation_reason'] ?? null,
                'status'              => $data['status'],
                'happen_in'           => now(),
                'appointment_id'      => $appointment->id,
                'actor_id'            => auth()->user()->id,
                'affected_id'         => $data['customer_id'] ?? $appointment->customer_id,
                'event'               => "appointment has been Updated in " . now()->format('Y-m-d H:i:s') . " By " . auth()->user()->full_name->en
            ]);
        }
    }
}
