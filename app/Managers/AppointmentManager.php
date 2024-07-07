<?php

namespace App\Managers;

use App\Enums\AppointmentDeductionStatusEnum;
use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use App\Enums\ClinicTransactionStatusEnum;
use App\Enums\ClinicTransactionTypeEnum;
use App\Enums\OfferTypeEnum;
use App\Enums\RolesPermissionEnum;
use App\Jobs\UpdateAppointmentRemainingTimeJob;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Offer;
use App\Models\SystemOffer;
use App\Notifications\Clinic\NewOnlineAppointmentNotification;
use App\Notifications\Customer\CustomerAppointmentChangedNotification;
use App\Notifications\RealTime\AppointmentStatusChangeNotification;
use App\Repositories\AppointmentDeductionRepository;
use App\Repositories\AppointmentLogRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\ClinicRepository;
use App\Repositories\ClinicTransactionRepository;
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

        if ($data['type'] != AppointmentTypeEnum::ONLINE->value) {
            $systemOffersIds = [];
            $systemOffersTotal = 0;
        }

        $data['total_cost'] = $this->calculateAppointmentTotalCost($data, $servicePrice, $systemOffersTotal, $clinicOffersTotal, $clinic);

        $appointment = AppointmentRepository::make()->create($data);

        if (
            $appointment->type == AppointmentTypeEnum::ONLINE->value
            && !in_array($appointment->status, [
                AppointmentStatusEnum::CANCELLED->value,
                AppointmentStatusEnum::PENDING->value,
            ])
        ) {
            $this->addDeductionCostTransactions($clinic, $systemOffersTotal, $appointment);
        }

        if ($appointment->status == AppointmentStatusEnum::CHECKOUT->value) {
            ClinicTransactionRepository::make()
                ->create([
                    'amount'         => $appointment->total_cost,
                    'appointment_id' => $appointment->id,
                    'type'           => ClinicTransactionTypeEnum::INCOME->value,
                    'clinic_id'      => $clinic->id,
                    'notes'          => "An income from the cost of the appointment with id : $appointment->id , Patient name : {$appointment->customer->user->full_name}",
                    'status'         => ClinicTransactionStatusEnum::DONE->value,
                    'date'           => now(),
                ]);
        }

        if (isset($systemOffersIds)) {
            $appointment->systemOffers()->sync($systemOffersIds);
            $appointment->customer->systemOffers()->sync($systemOffersIds);
        }
        if (isset($clinicOffersIds)) {
            $appointment->offers()->sync($clinicOffersIds);
        }

        if ($appointment->type == AppointmentTypeEnum::ONLINE->value
            && $appointment->status == AppointmentStatusEnum::PENDING->value) {
            FirebaseServices::make()
                ->setData([
                    'appointment_id' => $appointment->id,
                    'message'        => "You have new online appointment at {$appointment->date->format('Y-m-d')}",
                ])
                ->setMethod(FirebaseServices::MANY)
                ->setTo([$appointment->clinic->user->id, ...$clinic->clinicEmployees->pluck('user_id')->toArray()])
                ->setNotification(NewOnlineAppointmentNotification::class)
                ->send();
        }

        $this->logAppointment($data, $appointment);
        return $appointment->load($relationships)->loadCount($countable);
    }

    public function update(array $data, $id, array $relationships = [], array $countable = [])
    {
        $appointment = AppointmentRepository::make()->find($id);

        if (!$appointment?->canUpdate()) {
            return null;
        }

        $oldStatus = $appointment->status;
        if (isset($data['status'])
            && $data['status'] != $appointment->status
            && auth()->user()?->isClinic()
            && !$this->canUpdateOnlineAppointmentStatus($appointment, $data['status'])) {
            return null;
        }
        $prevAppointmentType = $appointment->type;
        $clinic = $appointment->clinic;
        $this->logAppointment($data, $appointment, true);
        $servicePrice = $this->getServiceCost($data, $appointment);
        [$clinicOffersTotal, $clinicOffersIds, $systemOffersTotal, $systemOffersIds] = $this->handleAppointmentOffers($data, $clinic->appointment_cost, $appointment);
        $data['total_cost'] = $this->calculateAppointmentTotalCost($data, $servicePrice, $systemOffersTotal, $clinicOffersTotal, $clinic, $appointment);
        $prevStatus = $appointment->status;
        $appointment = AppointmentRepository::make()->update($data, $appointment);

        if (isset($systemOffersIds)) {
            $appointment->systemOffers()->sync($systemOffersIds);
            $appointment->customer->systemOffers()->detach();
            $appointment->customer->systemOffers()->sync($systemOffersIds);
        }
        if (isset($clinicOffersIds)) {
            $appointment->offers()->sync($clinicOffersIds);
        }

        $this->handleAppointmentRemainingTime($appointment, $prevStatus);
        $this->checkoutPreviousAppointmentsIfNewStatusIsCheckin($appointment, $prevStatus);
        $this->handleChangeAppointmentNotifications($appointment, $oldStatus);
        $this->handleTransactionsWhenChangeStatus($appointment->refresh(), $prevStatus);

        if ($appointment->type == AppointmentTypeEnum::ONLINE->value
            && $prevAppointmentType != AppointmentTypeEnum::ONLINE->value) {
            FirebaseServices::make()
                ->setData([
                    'appointment_id' => $appointment->id,
                    'message'        => "You have new online appointment at {$appointment->date->format('Y-m-d')}",
                ])
                ->setMethod(FirebaseServices::MANY)
                ->setTo([$appointment->clinic->user->id, ...$clinic->clinicEmployees->pluck('user_id')->toArray()])
                ->setNotification(NewOnlineAppointmentNotification::class)
                ->send();
        }

        return $appointment->load($relationships)->loadCount($countable);
    }

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

    private function handleAppointmentOffers(array $data, $appointmentCost, ?Appointment $appointment = null): array
    {
        $clinicOffersTotal = 0;
        $clinicOffersIds = [];
        if (isset($data['offers'])) {
            $clinicOffers = OfferRepository::make()
                ->getByIds($data['offers'], $data['clinic_id'] ?? $appointment?->clinic_id);
            $clinicOffersTotal = $clinicOffers
                ->sum(fn(Offer $offer) => $offer->type == OfferTypeEnum::FIXED->value
                    ? $offer->value
                    : ($offer->value * $appointmentCost) / 100
                );
            $clinicOffersIds = $clinicOffers->pluck('id')->toArray();
        } elseif ($appointment) {
            $clinicOffersTotal = $appointment->getClinicOfferTotal();
            $clinicOffersIds = null;
        }

        $systemOffersTotal = 0;
        $systemOffersIds = [];
        if (isset($data['system_offers'])) {
            $systemOffers = SystemOfferRepository::make()
                ->getByIds($data['system_offers'], $data['clinic_id'] ?? $appointment?->clinic_id);
            $systemOffersTotal = $systemOffers
                ->sum(fn(SystemOffer $offer) => $offer->type == OfferTypeEnum::FIXED->value
                    ? $offer->amount
                    : ($offer->amount * $appointmentCost) / 100
                );
            $systemOffersIds = $systemOffers->pluck('id')->toArray();
        } elseif ($appointment) {
            $systemOffersTotal = $appointment->getSystemOffersTotal();
            $systemOffersIds = null;
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
                'event'               => "appointment has been created in " . now()->format('Y-m-d H:i:s') . " By " . auth()->user()?->full_name->en
            ]);
        } else {
            AppointmentLogRepository::make()->create([
                'cancellation_reason' => $data['cancellation_reason'] ?? null,
                'status'              => $data['status'],
                'happen_in'           => now(),
                'appointment_id'      => $appointment->id,
                'actor_id'            => auth()->user()?->id,
                'affected_id'         => $data['customer_id'] ?? $appointment->customer_id,
                'event'               => "appointment has been Updated in " . now()->format('Y-m-d H:i:s') . " By " . auth()->user()?->full_name->en
            ]);
        }
    }

    public function handleTransactionsWhenChangeStatus(Appointment $appointment, string $prevStatus): void
    {
        if (
            $prevStatus != AppointmentStatusEnum::CANCELLED->value
            && $appointment->status == AppointmentStatusEnum::CANCELLED->value
        ) {
            $appointment->appointmentDeduction?->clinicTransaction()->delete();
            $appointment->appointmentDeduction()->delete();
            $appointment->clinicTransaction()->delete();
            $appointment->customer->systemOffers()->detach();
        } elseif (
            $prevStatus == AppointmentStatusEnum::PENDING->value
            && $appointment->status == AppointmentStatusEnum::BOOKED->value
            && $appointment->type == AppointmentTypeEnum::ONLINE->value
        ) {
            $this->addDeductionCostTransactions($appointment->clinic, $appointment->getSystemOffersTotal(), $appointment);
        } elseif (
            $prevStatus != AppointmentStatusEnum::CHECKOUT->value
            && $appointment->status == AppointmentStatusEnum::CHECKOUT->value
        ) {
            ClinicTransactionRepository::make()
                ->create([
                    'amount'         => $appointment->total_cost,
                    'appointment_id' => $appointment->id,
                    'type'           => ClinicTransactionTypeEnum::INCOME->value,
                    'clinic_id'      => $appointment->clinic_id,
                    'notes'          => "An income from the cost of the appointment with id : $appointment->id , Patient name : {$appointment->customer->user->full_name}",
                    'status'         => ClinicTransactionStatusEnum::DONE->value,
                    'date'           => now(),
                ]);
        } elseif (
            $prevStatus == AppointmentStatusEnum::CHECKOUT->value
            && $appointment->status != AppointmentStatusEnum::CHECKOUT->value
        ) {
            $appointment->clinicTransaction()->delete();
        } elseif (
            $prevStatus == AppointmentStatusEnum::CANCELLED->value
            && !in_array($appointment->status, [AppointmentStatusEnum::CANCELLED->value, AppointmentStatusEnum::PENDING])
            && $appointment->type == AppointmentTypeEnum::ONLINE->value
        ) {
            $this->addDeductionCostTransactions($appointment->clinic, $appointment->getSystemOffersTotal(), $appointment);
            $appointment
                ->customer
                ->systemOffers()
                ->sync($appointment->systemOffers->pluck('id')->toArray());

            if ($appointment->status == AppointmentStatusEnum::CHECKOUT->value) {
                ClinicTransactionRepository::make()
                    ->create([
                        'amount'         => $appointment->total_cost,
                        'appointment_id' => $appointment->id,
                        'type'           => ClinicTransactionTypeEnum::INCOME->value,
                        'clinic_id'      => $appointment->clinic_id,
                        'notes'          => "An income from the cost of the appointment with id : $appointment->id , Patient name : {$appointment->customer->user->full_name}",
                        'status'         => ClinicTransactionStatusEnum::DONE->value,
                        'date'           => now(),
                    ]);
            }
        }
    }

    private function addDeductionCostTransactions(Clinic $clinic, mixed $systemOffersTotal, Appointment $appointment): void
    {
        $deductionAmount = $clinic->deduction_cost - $systemOffersTotal;
        $clinicTransactionType = $deductionAmount > 0
            ? ClinicTransactionTypeEnum::SYSTEM_DEBT->value
            : ClinicTransactionTypeEnum::DEBT_TO_ME->value;

        $clinicTransaction = ClinicTransactionRepository::make()->create([
            'amount'         => $deductionAmount,
            'appointment_id' => $appointment->id,
            'type'           => $clinicTransactionType,
            'clinic_id'      => $clinic->id,
            'notes'          => "An Appointment Deduction For The Appointment With Id : $appointment->id , Patient name : {$appointment->customer->user->full_name}",
            'status'         => ClinicTransactionStatusEnum::PENDING->value,
            'date'           => now(),
        ]);

        AppointmentDeductionRepository::make()->create([
            'amount'                => $clinic->deduction_cost - $systemOffersTotal,
            'status'                => AppointmentDeductionStatusEnum::PENDING->value,
            'clinic_transaction_id' => $clinicTransaction->id,
            'appointment_id'        => $appointment->id,
            'clinic_id'             => $clinic->id,
            'date'                  => now(),
        ]);
    }
}
