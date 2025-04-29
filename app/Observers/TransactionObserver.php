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
        $latestBalance = BalanceRepository::make()->getBalance()?->balance ?? 0;
        if ($prevTransaction['type'] == TransactionTypeEnum::OUTCOME->value && $transaction->isPlus()) {
            $balance = ($latestBalance + $prevTransaction['amount']) + $transaction->amount;
        } elseif ($prevTransaction['type'] == TransactionTypeEnum::INCOME->value && $transaction->isMinus()) {
            $balance = ($latestBalance - $prevTransaction['amount']) - $transaction->amount;
        } elseif ($prevTransaction['amount'] != $transaction->amount) {
            if ($transaction->isPlus()) {
                $balance = ($latestBalance - $prevTransaction['amount']) + $transaction->amount;
            } elseif ($transaction->isMinus()) {
                $balance = ($latestBalance + $prevTransaction['amount']) - $transaction->amount;
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
