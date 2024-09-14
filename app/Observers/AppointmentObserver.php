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
use App\Notifications\Clinic\NewOnlineAppointmentNotification;
use App\Notifications\Customer\CustomerAppointmentChangedNotification;
use App\Notifications\RealTime\AppointmentChangeNotification;
use App\Notifications\RealTime\NewAppointmentNotification;
use App\Repositories\AppointmentRepository;
use App\Repositories\ClinicTransactionRepository;
use App\Services\FirebaseServices;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
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

        if ($appointment->type == AppointmentTypeEnum::ONLINE->value) {
            FirebaseServices::make()
                ->setData([
                    'appointment' => $appointment,
                ])
                ->setMethod(FirebaseServices::MANY)
                ->setTo([$appointment->clinic?->user?->id, ...$appointment->clinic?->clinicEmployees?->pluck('user_id')->toArray()])
                ->setNotification(NewOnlineAppointmentNotification::class)
                ->send();
        }

        FirebaseServices::make()
            ->setData([])
            ->setMethod(FirebaseServices::MANY)
            ->setTo([$appointment->clinic?->user?->id, ...$appointment->clinic?->clinicEmployees?->pluck('user_id')->toArray()])
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
                ->setTo([$appointment->clinic->user->id, ...$appointment->clinic?->clinicEmployees->pluck('user_id')->toArray()])
                ->setNotification(NewOnlineAppointmentNotification::class)
                ->send();
        }
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
            $appointment->save();
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
            AppointmentManager::make()
                ->addDeductionCostTransactions($appointment->clinic, $appointment->getSystemOffersTotal(), $appointment);
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
            AppointmentManager::make()
                ->addDeductionCostTransactions($appointment->clinic, $appointment->getSystemOffersTotal(), $appointment);
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
}
