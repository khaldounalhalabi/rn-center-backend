<?php

namespace App\Observers;

use App\Enums\ClinicTransactionStatusEnum;
use App\Enums\ClinicTransactionTypeEnum;
use App\Models\Balance;
use App\Models\Clinic;
use App\Models\ClinicTransaction;
use App\Models\User;
use App\Notifications\RealTime\BalanceChangeNotification;
use App\Services\FirebaseServices;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class ClinicTransactionObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the ClinicTransaction "created" event.
     */
    public function created(ClinicTransaction $transaction): void
    {
        $clinic = $transaction->clinic;
        if ($transaction->status == ClinicTransactionStatusEnum::DONE->value) {
            $latestBalance = $clinic->balance;
            $this->handleTheAdditionOfNewBalanceRecord($transaction, $latestBalance, $clinic);
        }
    }

    /**
     * Handle the ClinicTransaction "updated" event.
     */
    public function updated(ClinicTransaction $clinicTransaction): void
    {
        //
    }

    public function updating(ClinicTransaction $transaction): void
    {
        $prevTransaction = $transaction->getOriginal();
        $clinic = $transaction->clinic;
        $latestBalance = $clinic->balance;
        /**
         * prev status is done and current status is done and the two amounts are not the same
         */
        if ($prevTransaction['status'] == ClinicTransactionStatusEnum::DONE->value
            && $transaction->status == ClinicTransactionStatusEnum::DONE->value
            && $prevTransaction['amount'] != $transaction->amount
        ) {
            if (in_array($transaction->type, [ClinicTransactionTypeEnum::INCOME->value, ClinicTransactionTypeEnum::DEBT_TO_ME->value])) {
                $balance = (($latestBalance?->balance ?? 0) - $prevTransaction['amount']) + $transaction->amount;
                $note = "[EDIT] " . $transaction->notes;
            } elseif (in_array($transaction->type, [ClinicTransactionTypeEnum::OUTCOME->value, ClinicTransactionTypeEnum::SYSTEM_DEBT->value])) {
                $balance = (($latestBalance?->balance ?? 0) + $prevTransaction['amount']) - $transaction->amount;
                $note = "[EDIT] " . $transaction->notes;
            }
            if (isset($balance)) {
                $newBalance = Balance::create([
                    'balance'          => $balance,
                    'balanceable_id'   => $clinic->id,
                    'balanceable_type' => Clinic::class,
                    'note'             => $note ?? "",
                ]);
                $transaction->updateQuietly([
                    'after_balance'  => $newBalance?->balance ?? 0,
                    'before_balance' => $latestBalance?->balance ?? 0,
                ]);
                $this->sendBalanceChangeNotification($newBalance->balance, $clinic->id);
            }
        } /**
         * prev status is pending and the current status is done
         * then handle the latest amount because we won't care about the prev amount because
         * it didn't has effect on the balance because it was pending
         */
        elseif ($prevTransaction['status'] == ClinicTransactionStatusEnum::PENDING->value
            && $transaction->status == ClinicTransactionStatusEnum::DONE->value) {
            $this->handleTheAdditionOfNewBalanceRecord($transaction, $latestBalance, $clinic);
            $this->sendBalanceChangeNotification($latestBalance?->balance ?? 0, $clinic->id);
        } /**
         * prev status is done and the current is pending then we remove it from the balance
         */
        elseif ($prevTransaction['status'] == ClinicTransactionStatusEnum::DONE->value
            && $transaction->status == ClinicTransactionStatusEnum::PENDING->value) {
            if (in_array($transaction->type, [ClinicTransactionTypeEnum::INCOME->value, ClinicTransactionTypeEnum::DEBT_TO_ME->value])) {
                $balance = ($latestBalance?->balance ?? 0) - $transaction->amount;
                $note = "[EDIT][CHANGING_STATUS] " . $transaction->notes;
            } elseif (in_array($transaction->type, [ClinicTransactionTypeEnum::OUTCOME->value, ClinicTransactionTypeEnum::SYSTEM_DEBT->value])) {
                $balance = ($latestBalance?->balance ?? 0) + $transaction->amount;
                $note = "[EDIT][CHANGING_STATUS] " . $transaction->notes;
            }
            if (isset($balance)) {
                $newBalance = Balance::create([
                    'balance'          => $balance,
                    'balanceable_id'   => $clinic->id,
                    'balanceable_type' => Clinic::class,
                    'note'             => $note ?? "",
                ]);
                $transaction->updateQuietly([
                    'after_balance'  => $newBalance?->balance ?? 0,
                    'before_balance' => $latestBalance?->balance ?? 0,
                ]);
                $this->sendBalanceChangeNotification($newBalance->balance, $clinic->id);
            }
        } /**
         * the prev type is an "outcome" type and the current is "income" type
         * and in both cases the transaction in "done" status
         * then we remove the "outcome" and add the "income"
         */
        elseif (in_array($prevTransaction['type'], [ClinicTransactionTypeEnum::OUTCOME->value, ClinicTransactionTypeEnum::SYSTEM_DEBT->value])
            && $prevTransaction['status'] == ClinicTransactionStatusEnum::DONE->value
            && in_array($transaction->type, [ClinicTransactionTypeEnum::INCOME->value, ClinicTransactionTypeEnum::DEBT_TO_ME->value])
            && $transaction->status == ClinicTransactionStatusEnum::DONE->value) {
            $balance = (($latestBalance?->balance ?? 0) + $prevTransaction['amount']) + $transaction->amount;
            $note = "[EDIT] " . $transaction->notes;
            $newBalance = Balance::create([
                'balance'          => $balance,
                'balanceable_id'   => $clinic->id,
                'balanceable_type' => Clinic::class,
                'note'             => $note ?? "",
            ]);
            $transaction->updateQuietly([
                'after_balance'  => $newBalance?->balance ?? 0,
                'before_balance' => $latestBalance?->balance ?? 0,
            ]);
            $this->sendBalanceChangeNotification($newBalance->balance, $clinic->id);
        } /**
         * the prev type is an "income" type and the current is "outcome" type
         * and in both cases the transaction in "done" status
         * then we remove the "income" and add the "outcome"
         */
        elseif (in_array($prevTransaction['type'], [ClinicTransactionTypeEnum::INCOME->value, ClinicTransactionTypeEnum::DEBT_TO_ME->value])
            && $prevTransaction['status'] == ClinicTransactionStatusEnum::DONE->value
            && in_array($transaction->type, [ClinicTransactionTypeEnum::OUTCOME->value, ClinicTransactionTypeEnum::SYSTEM_DEBT->value])
            && $transaction->status == ClinicTransactionStatusEnum::DONE->value) {
            $balance = (($latestBalance?->balance ?? 0) - $prevTransaction['amount']) - $transaction->amount;
            $note = "[EDIT] " . $transaction->notes;
            $newBalance = Balance::create([
                'balance'          => $balance,
                'balanceable_id'   => $clinic->id,
                'balanceable_type' => Clinic::class,
                'note'             => $note ?? "",
            ]);
            $transaction->updateQuietly([
                'after_balance'  => $newBalance?->balance ?? 0,
                'before_balance' => $latestBalance?->balance ?? 0,
            ]);
            $this->sendBalanceChangeNotification($newBalance->balance, $clinic->id);
        }
    }

    /**
     * Handle the ClinicTransaction "deleted" event.
     */
    public function deleted(ClinicTransaction $transaction): void
    {
        if ($transaction->status == ClinicTransactionStatusEnum::DONE->value) {
            $clinic = $transaction->clinic;
            $latestBalance = $clinic?->balance?->balance ?? 0;
            if (in_array($transaction->type, [ClinicTransactionTypeEnum::INCOME->value, ClinicTransactionTypeEnum::DEBT_TO_ME->value])) {
                $balance = $latestBalance - $transaction->amount;
                $note = "[DELETED] " . $transaction->notes ?? "";
            } elseif (in_array($transaction->type, [ClinicTransactionTypeEnum::OUTCOME->value, ClinicTransactionTypeEnum::SYSTEM_DEBT->value])) {
                $balance = $latestBalance + $transaction->amount;
                $note = "[DELETED] " . $transaction->notes ?? "";
            }

            if (isset($balance)) {
                $newBalance = Balance::create([
                    'balance'          => $balance,
                    'note'             => $note ?? "",
                    'balanceable_type' => Clinic::class,
                    'balanceable_id'   => $clinic->id,
                ]);
                $this->sendBalanceChangeNotification($newBalance->balance, $clinic->id);
            }
        }
    }

    /**
     * Handle the ClinicTransaction "restored" event.
     */
    public function restored(ClinicTransaction $clinicTransaction): void
    {
        //
    }

    /**
     * Handle the ClinicTransaction "force deleted" event.
     */
    public function forceDeleted(ClinicTransaction $transaction): void
    {
        if ($transaction->status == ClinicTransactionStatusEnum::DONE->value) {
            $clinic = $transaction->clinic;
            $latestBalance = $clinic?->balance?->balance ?? 0;
            if (in_array($transaction->type, [ClinicTransactionTypeEnum::INCOME->value, ClinicTransactionTypeEnum::DEBT_TO_ME->value])) {
                $balance = $latestBalance - $transaction->amount;
                $note = "[DELETED] " . $transaction->notes ?? "";
            } elseif (in_array($transaction->type, [ClinicTransactionTypeEnum::OUTCOME->value, ClinicTransactionTypeEnum::SYSTEM_DEBT->value])) {
                $balance = $latestBalance + $transaction->amount;
                $note = "[DELETED] " . $transaction->notes ?? "";
            }

            if (isset($balance)) {
                $newBalance = Balance::create([
                    'balance'          => $balance,
                    'note'             => $note ?? "",
                    'balanceable_type' => Clinic::class,
                    'balanceable_id'   => $clinic->id,
                ]);
                $this->sendBalanceChangeNotification($newBalance->balance, $clinic->id);
                Log::info("################ Delete Transaction Notification Send ################");
            }
        }
    }

    private function sendBalanceChangeNotification($balance, $clinicId): void
    {
        FirebaseServices::make()
            ->setData([
                'balance' => $balance,
            ])
            ->setMethod(FirebaseServices::ToQuery)
            ->setTo(
                User::whereHas('clinic', function (Builder $query) use ($clinicId) {
                    $query->where('clinics.id', $clinicId);
                })->orWhereHas('clinicEmployee', function (Builder $builder) use ($clinicId) {
                    $builder->where('clinic_id', $clinicId);
                })
            )->setNotification(BalanceChangeNotification::class)
            ->send();
    }

    /**
     * @param ClinicTransaction $transaction
     * @param Balance|null      $latestBalance
     * @param Clinic            $clinic
     * @return void
     */
    private function handleTheAdditionOfNewBalanceRecord(ClinicTransaction $transaction, ?Balance $latestBalance, Clinic $clinic): void
    {
        if (in_array($transaction->type, [ClinicTransactionTypeEnum::INCOME->value, ClinicTransactionTypeEnum::DEBT_TO_ME->value])) {
            $balance = ($latestBalance?->balance ?? 0) + $transaction->amount;
            $note = $transaction->notes;
        } elseif (in_array($transaction->type, [ClinicTransactionTypeEnum::OUTCOME->value, ClinicTransactionTypeEnum::SYSTEM_DEBT->value])) {
            $balance = ($latestBalance?->balance ?? 0) - $transaction->amount;
            $note = $transaction->notes;
        }
        if (isset($balance)) {
            $newBalance = Balance::create([
                'balance'          => $balance,
                'balanceable_id'   => $clinic->id,
                'balanceable_type' => Clinic::class,
                'note'             => $note ?? "",
            ]);
            $transaction->updateQuietly([
                'after_balance'  => $newBalance?->balance ?? 0,
                'before_balance' => $latestBalance?->balance ?? 0,
            ]);
            $this->sendBalanceChangeNotification($newBalance->balance, $clinic->id);
        }
    }
}
