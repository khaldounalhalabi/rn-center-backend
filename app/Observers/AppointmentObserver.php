<?php

namespace App\Observers;

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use App\Enums\RolesPermissionEnum;
use App\Jobs\UpdateAppointmentRemainingTimeJob;
use App\Models\Appointment;
use App\Notifications\Clinic\NewOnlineAppointmentNotification;
use App\Notifications\Customer\CustomerAppointmentChangedNotification;
use App\Notifications\RealTime\AppointmentChangeNotification;
use App\Notifications\RealTime\BalanceChangeNotification;
use App\Notifications\RealTime\NewAppointmentNotification;
use App\Repositories\AppointmentRepository;
use App\Services\FirebaseServices;

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
        if ($appointment->type == AppointmentTypeEnum::ONLINE->value) {
            FirebaseServices::make()
                ->setData([
                    'appointment' => $appointment,
                ])
                ->setMethod(FirebaseServices::MANY)
                ->setTo([$appointment->clinic?->user?->id])
                ->setNotification(NewOnlineAppointmentNotification::class)
                ->send();
        }

        FirebaseServices::make()
            ->setData([])
            ->setMethod(FirebaseServices::MANY)
            ->setTo([$appointment->clinic?->user?->id])
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
                ->setTo([$appointment->clinic->user->id])
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
        FirebaseServices::make()
            ->setData([])
            ->setMethod(FirebaseServices::MANY)
            ->setTo([$appointment->clinic?->user?->id])
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
