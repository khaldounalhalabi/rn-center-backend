<?php

namespace App\Observers;

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use App\Enums\ClinicTransactionStatusEnum;
use App\Enums\ClinicTransactionTypeEnum;
use App\Enums\RolesPermissionEnum;
use App\Jobs\UpdateAppointmentRemainingTimeJob;
use App\Managers\AppointmentManager;
use App\Models\Appointment;
use App\Models\AppointmentDeduction;
use App\Models\ClinicTransaction;
use App\Notifications\Clinic\NewOnlineAppointmentNotification;
use App\Notifications\Customer\CustomerAppointmentChangedNotification;
use App\Notifications\RealTime\AppointmentChangeNotification;
use App\Notifications\RealTime\BalanceChangeNotification;
use App\Notifications\RealTime\NewAppointmentNotification;
use App\Repositories\AppointmentRepository;
use App\Repositories\ClinicTransactionRepository;
use App\Services\FirebaseServices;
use Illuminate\Support\Collection;

class AppointmentObserver
{
    /**
     * handle the Appointment "creating" event.
     * @param Appointment $appointment
     * @return void
     */
    public function creating(Appointment $appointment): void
    {
        $code = uniqid();
        while (AppointmentRepository::make()->codeExists($code)) {
            $code = uniqid();
        }
        $appointment->appointment_unique_code = $code;
    }

    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        if ($appointment->status == AppointmentStatusEnum::CHECKOUT->value) {
            ClinicTransactionRepository::make()
                ->create([
                    'amount' => $appointment->total_cost,
                    'appointment_id' => $appointment->id,
                    'type' => ClinicTransactionTypeEnum::INCOME->value,
                    'clinic_id' => $appointment->clinic_id,
                    'notes' => "An income from the cost of the appointment with id : $appointment->id , Patient name : {$appointment?->customer?->user?->fullName}",
                    'status' => ClinicTransactionStatusEnum::DONE->value,
                    'date' => now(),
                ]);
        }

        if ($appointment->type == AppointmentTypeEnum::ONLINE->value) {
            FirebaseServices::make()
                ->setData([
                    'appointment' => $appointment,
                ])
                ->setMethod(FirebaseServices::MANY)
                ->setTo([$appointment->clinic?->user?->id, ...$appointment->clinic?->clinicEmployees?->pluck('user_id')?->toArray() ?? []])
                ->setNotification(NewOnlineAppointmentNotification::class)
                ->send();
        }

        FirebaseServices::make()
            ->setData([])
            ->setMethod(FirebaseServices::MANY)
            ->setTo([$appointment->clinic?->user?->id, ...$appointment->clinic?->clinicEmployees?->pluck('user_id')?->toArray() ?? []])
            ->setNotification(NewAppointmentNotification::class)
            ->send();

        FirebaseServices::make()
            ->setData([])
            ->setMethod(FirebaseServices::ByRole)
            ->setRole(RolesPermissionEnum::ADMIN['role'])
            ->setNotification(NewAppointmentNotification::class)
            ->send();
    }


    /**
     * Handle the Appointment "updating" event.
     */
    public function updating(Appointment $appointment): void
    {
        $prevAppointment = $appointment->getOriginal();
        $oldStatus = $prevAppointment['status'] ?? null;
        $oldType = $prevAppointment['type'] ?? null;

        if (!$oldStatus || !$oldType) return;

        $this->handleChangeAppointmentNotifications($appointment, $oldStatus);
        $this->checkoutPreviousAppointmentsIfNewStatusIsCheckin($appointment, $oldStatus);
        $this->handleAppointmentRemainingTime($appointment, $oldStatus);
        $this->handleTransactionsWhenChangeStatus($appointment, $oldStatus);

        if ($appointment->type == AppointmentTypeEnum::ONLINE->value
            && $oldType != AppointmentTypeEnum::ONLINE->value) {
            FirebaseServices::make()
                ->setData([
                    'appointment_id' => $appointment,
                ])->setMethod(FirebaseServices::MANY)
                ->setTo([$appointment->clinic->user->id, ...$appointment->clinic?->clinicEmployees?->pluck('user_id')?->toArray() ?? []])
                ->setNotification(NewOnlineAppointmentNotification::class)
                ->send();
        }
    }

    private function handleChangeAppointmentNotifications(Appointment $appointment, ?string $oldStatus = null): void
    {
        if ($oldStatus != $appointment->status) {
            FirebaseServices::make()
                ->setData([
                    'appointment' => $appointment,
                ])
                ->setMethod(FirebaseServices::ONE)
                ->setTo($appointment->customer->user)
                ->setNotification(CustomerAppointmentChangedNotification::class)
                ->send();
        }

        FirebaseServices::make()
            ->setData([
                'appointment' => $appointment,
            ])
            ->setMethod(FirebaseServices::ByRole)
            ->setRole(RolesPermissionEnum::ADMIN['role'])
            ->setNotification(AppointmentChangeNotification::class)
            ->send();

        FirebaseServices::make()
            ->setData([
                'appointment' => $appointment,
            ])->setMethod(FirebaseServices::MANY)
            ->setTo([
                $appointment->clinic?->user?->id,
                ...($appointment->clinic?->clinicEmployees?->pluck('user_id')?->toArray() ?? []),
            ])->setNotification(AppointmentChangeNotification::class)
            ->send();
    }

    private function checkoutPreviousAppointmentsIfNewStatusIsCheckin(mixed $appointment, string $prevStatus): void
    {
        if (
            $appointment->status == AppointmentStatusEnum::CHECKIN->value
            && $prevStatus != AppointmentStatusEnum::CHECKIN->value
        ) {
            AppointmentRepository::make()->updatePreviousCheckinClinicAppointments($appointment->clinic_id,
                $appointment->appointment_sequence,
                $appointment->date,
                [
                    'status' => AppointmentStatusEnum::CHECKOUT->value,
                ]);
        }
    }

    public function handleAppointmentRemainingTime(Appointment $appointment, string $prevStatus): void
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
            $appointment->saveQuietly();
        }
    }

    public function handleTransactionsWhenChangeStatus(Appointment $appointment, string $prevStatus): void
    {
        //TODO::make it handle every update so if the admin changed the system offers it recalculate the deduction
        if ($prevStatus != AppointmentStatusEnum::CHECKOUT->value
            && $appointment->status == AppointmentStatusEnum::CHECKOUT->value) {
            ClinicTransaction::create([
                'appointment_id' => $appointment->id,
                'status' => ClinicTransactionStatusEnum::DONE->value,
                'type' => ClinicTransactionTypeEnum::INCOME->value,
                'clinic_id' => $appointment->clinic_id,
                'date' => now(),
                'amount' => $appointment->total_cost,
                'notes' => "An income from the cost of the appointment with id : $appointment->id , Patient name : {$appointment->customer->user->fullName}",
            ]);

            if ($appointment->type == AppointmentTypeEnum::ONLINE->value) {
                AppointmentManager::make()
                    ->addDeductionCostTransactions($appointment->clinic, $appointment->getSystemOffersTotal(), $appointment);
            }
        } elseif ($prevStatus == AppointmentStatusEnum::CHECKOUT->value
            && $appointment->status != AppointmentStatusEnum::CHECKOUT->value
        ) {
            ClinicTransaction::where('appointment_id', $appointment->id)
                ->chunk(10, function (/** @var Collection<ClinicTransaction> $transactions */ $transactions) {
                    $transactions->each(fn(ClinicTransaction $clinicTransaction) => $clinicTransaction->delete());
                });

            AppointmentDeduction::where('appointment_id', $appointment->id)
                ->chunk(10, function (/** @var Collection<AppointmentDeduction> $deductions */ $deductions) {
                    $deductions->each(function (AppointmentDeduction $deduction) {
                        $deduction->clinicTransaction?->delete();
                        $deduction->delete();
                    });
                });

            if ($appointment->status == AppointmentStatusEnum::CANCELLED->value) {
                $appointment->customer->systemOffers()->detach();
            }
        }

        FirebaseServices::make()
            ->setData([])
            ->setMethod(FirebaseServices::MANY)
            ->setTo([$appointment->clinic?->user?->id, ...$appointment->clinic?->clinicEmployees?->pluck('user_id')?->toArray() ?? []])
            ->setNotification(BalanceChangeNotification::class)
            ->send();

        FirebaseServices::make()
            ->setData([])
            ->setMethod(FirebaseServices::ByRole)
            ->setRole(RolesPermissionEnum::ADMIN['role'])
            ->setNotification(BalanceChangeNotification::class)
            ->send();
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        //
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        //
    }
}
