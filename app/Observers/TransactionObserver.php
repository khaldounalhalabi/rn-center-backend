<?php

namespace App\Observers;

use App\Enums\PayrunStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use App\Repositories\BalanceRepository;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class TransactionObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $latestBalance = BalanceRepository::make()->getBalance()?->balance ?? 0;
        if ($transaction->isIncome()) {
            $balance = $latestBalance + $transaction->amount;
        } elseif ($transaction->isOutcome()) {
            $balance = $latestBalance - $transaction->amount;
        }

        if (isset($balance)) {
            BalanceRepository::make()->create([
                'balance' => $balance,
            ]);
        }
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "updating" event.
     */
    public function updating(Transaction $transaction): void
    {
        $prevTransaction = $transaction->getOriginal();
        $oldType = $prevTransaction['type'];
        $oldAmount = $prevTransaction['amount'];
        $latestBalance = BalanceRepository::make()->getBalance()?->balance ?? 0;
        if ($oldType == TransactionTypeEnum::OUTCOME->value && $transaction->isIncome()) {
            $balance = ($latestBalance + $oldAmount) + $transaction->amount;
        } elseif ($oldType == TransactionTypeEnum::INCOME->value && $transaction->isOutcome()) {
            $balance = ($latestBalance - $oldAmount) - $transaction->amount;
        } elseif ($oldAmount != $transaction->amount) {
            if ($transaction->isIncome()) {
                $balance = ($latestBalance - $oldAmount) + $transaction->amount;
            } elseif ($transaction->isOutcome()) {
                $balance = ($latestBalance + $oldAmount) - $transaction->amount;
            }
        }

        if (isset($balance)) {
            BalanceRepository::make()->create([
                'balance' => $balance,
            ]);
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        $latestBalance = BalanceRepository::make()->getBalance()?->balance ?? 0;

        if ($transaction->isIncome()) {
            $balance = $latestBalance - $transaction->amount;
        } elseif ($transaction->isOutcome()) {
            $balance = $latestBalance + $transaction->amount;
        }

        if (isset($balance)) {
            BalanceRepository::make()->create([
                'balance' => $balance,
            ]);
        }

        if ($transaction->payrun) {
            $transaction->payrun->update([
                'status' => PayrunStatusEnum::APPROVED->value
            ]);
        }
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }
}
