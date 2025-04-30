<?php

namespace App\Observers;

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
        if ($transaction->isPlus()) {
            $balance = $latestBalance + $transaction->amount;
        } elseif ($transaction->isMinus()) {
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
        if ($oldType == TransactionTypeEnum::OUTCOME->value && $transaction->isPlus()) {
            $balance = ($latestBalance + $oldAmount) + $transaction->amount;
        } elseif ($oldType == TransactionTypeEnum::INCOME->value && $transaction->isMinus()) {
            $balance = ($latestBalance - $oldAmount) - $transaction->amount;
        } elseif ($oldAmount != $transaction->amount) {
            if ($transaction->isPlus()) {
                $balance = ($latestBalance - $oldAmount) + $transaction->amount;
            } elseif ($transaction->isMinus()) {
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

        if ($transaction->isPlus()) {
            $balance = $latestBalance - $transaction->amount;
        } elseif ($transaction->isMinus()) {
            $balance = $latestBalance + $transaction->amount;
        }

        if (isset($balance)) {
            BalanceRepository::make()->create([
                'balance' => $balance,
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
