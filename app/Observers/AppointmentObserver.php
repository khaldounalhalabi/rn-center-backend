<?php

namespace App\Observers;

use App\Enums\AppointmentStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Appointment;
use App\Repositories\TransactionRepository;

class AppointmentObserver
{
    /**
     * handle the Appointment "creating" event.
     * @param Appointment $appointment
     * @return void
     */
    public function creating(Appointment $appointment): void
    {

    }

    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        if (AppointmentStatusEnum::hasTransaction($appointment->status)) {
            TransactionRepository::make()->create([
                'type' => $appointment->total_cost >= 0
                    ? TransactionTypeEnum::INCOME->value
                    : TransactionTypeEnum::OUTCOME->value,
                'description' => "An income for an appointment between Dr. {$appointment->clinic?->user?->full_name} and {$appointment->customer?->user?->full_name}",
                'actor_id' => null,
                'date' => $appointment->date_time,
                'amount' => abs($appointment->total_cost),
            ]);
        }
    }


    /**
     * Handle the Appointment "updating" event.
     */
    public function updating(Appointment $appointment): void
    {
        $prevAppointment = $appointment->getOriginal();
        $oldStatus = $prevAppointment['status'] ?? null;
        $totalCost = $appointment->getTotalCost();

        if (!$oldStatus) return;

        if (AppointmentStatusEnum::hasTransaction($appointment->status)
            && !AppointmentStatusEnum::hasTransaction($oldStatus)) {
            TransactionRepository::make()->create([
                'type' => $totalCost >= 0
                    ? TransactionTypeEnum::INCOME->value
                    : TransactionTypeEnum::OUTCOME->value,
                'description' => "An income for an appointment between Dr. {$appointment->clinic?->user?->full_name} and {$appointment->customer?->user?->full_name}",
                'actor_id' => null,
                'date' => now(),
                'amount' => abs($totalCost),
                'appointment_id' => $appointment->id,
            ]);
        }

        if (!AppointmentStatusEnum::hasTransaction($appointment->status)
            && AppointmentStatusEnum::hasTransaction($oldStatus)) {
            TransactionRepository::make()->delete($appointment->transaction?->id);
        }

        if (AppointmentStatusEnum::hasTransaction($appointment->status)
            && AppointmentStatusEnum::hasTransaction($oldStatus)) {
            TransactionRepository::make()->update([
                'type' => $totalCost >= 0
                    ? TransactionTypeEnum::INCOME->value
                    : TransactionTypeEnum::OUTCOME->value,
                'amount' => abs($totalCost),
            ], $appointment->transaction?->id);
        }
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        TransactionRepository::make()->delete($appointment->transaction?->id);
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        //
    }
}
