<?php

namespace App\Observers;

use App\Enums\PayrunStatusEnum;
use App\Enums\RolesPermissionEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Payrun;
use App\Models\User;
use App\Modules\Notification\App\Enums\NotifyMethod;
use App\Modules\Notification\App\NotificationBuilder;
use App\Notifications\Realtime\PayrunStatusChangedNotification;
use App\Repositories\TransactionRepository;

class PayrunObserver
{
    /**
     * Handle the Payrun "created" event.
     */
    public function created(Payrun $payrun): void
    {
        //
    }

    /**
     * Handle the Payrun "updated" event.
     */
    public function updated(Payrun $payrun): void
    {
        //
    }

    /**
     * Handle the Payrun "deleted" event.
     */
    public function deleted(Payrun $payrun): void
    {
        //
    }

    /**
     * Handle the Payrun "restored" event.
     */
    public function restored(Payrun $payrun): void
    {
        //
    }

    public function updating(Payrun $payrun): void
    {
        $prevPayrun = $payrun->getOriginal();
        $newStatus = $payrun->status;
        $prevStatus = $prevPayrun['status'];

        if ($prevStatus != PayrunStatusEnum::DONE->value and $newStatus == PayrunStatusEnum::DONE->value) {
            $transaction = $payrun->transaction()->first();
            if ($transaction) {
                TransactionRepository::make()->delete($transaction);
            }

            if ($payrun->payment_cost >= 0) {
                $type = TransactionTypeEnum::OUTCOME->value;
            } else {
                $type = TransactionTypeEnum::INCOME->value;
            }

            TransactionRepository::make()->create([
                'payrun_id' => $payrun->id,
                'type' => $type,
                'date' => now(),
                'amount' => abs($payrun->payment_cost),
                'description' => "$payrun->period دفعة رواتب فترة ",
                'actor_id' => auth()->user()?->id ?? User::byRole(RolesPermissionEnum::ADMIN['role'])->first()?->id,
            ]);
        }

        if ($prevStatus == PayrunStatusEnum::DONE->value and $newStatus != PayrunStatusEnum::DONE->value) {
            $transaction = $payrun->transaction()->first();
            if ($transaction) {
                TransactionRepository::make()->delete($transaction);
            }
        }


        if ($prevStatus != $newStatus) {
            NotificationBuilder::make()
                ->data([])
                ->to(User::query()->whereNotNull('fcm_token'))
                ->method(NotifyMethod::TO_QUERY)
                ->notification(PayrunStatusChangedNotification::class)
                ->send();
        }
    }
}
