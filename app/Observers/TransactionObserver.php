<?php

namespace App\Observers;

use App\Enums\RolesPermissionEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Balance;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\RealTime\BalanceChangeNotification;
use App\Services\FirebaseServices;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $user = $transaction->actor;
        $latestBalance = $user->balance()?->balance ?? 0;
        if ($transaction->type == TransactionTypeEnum::INCOME->value) {
            $balance = $latestBalance + $transaction->amount;
            $note = $transaction->description ?? "";
        } elseif ($transaction->type == TransactionTypeEnum::OUTCOME->value) {
            $balance = $latestBalance - $transaction->amount;
            $note = $transaction->description ?? "";
        }
        if (isset($balance, $note)) {
            $newBalance = Balance::create([
                'balance'          => $balance,
                'note'             => $note,
                'balanceable_type' => User::class,
                'balanceable_id'   => $user->id
            ]);
            $this->sendBalanceChangeNotification($newBalance->balance);
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
        $user = $transaction->actor;
        $latestBalance = $user->balance()?->balance ?? 0;
        if ($prevTransaction['type'] == TransactionTypeEnum::OUTCOME->value
            && $transaction->type == TransactionTypeEnum::INCOME->value) {
            $balance = ($latestBalance + $prevTransaction['amount']) + $transaction->amount;
            $note = $transaction->description ?? "";
        } elseif ($prevTransaction['type'] == TransactionTypeEnum::INCOME->value
            && $transaction->type == TransactionTypeEnum::OUTCOME->value) {
            $balance = ($latestBalance - $prevTransaction['amount']) - $transaction->amount;
            $note = $transaction->description ?? "";
        } elseif ($prevTransaction['amount'] != $transaction->amount) {
            if ($transaction->type == TransactionTypeEnum::INCOME->value) {
                $balance = ($latestBalance - $prevTransaction['amount']) + $transaction->amount;
                $note = $transaction->description ?? "";
            } elseif ($transaction->type == TransactionTypeEnum::OUTCOME->value) {
                $balance = ($latestBalance + $prevTransaction['amount']) - $transaction->amount;
                $note = $transaction->description ?? "";
            }
        }

        if (isset($balance, $note)) {
            $newBalance = Balance::create([
                'balance'          => $balance,
                'note'             => $note,
                'balanceable_type' => User::class,
                'balanceable_id'   => $user->id
            ]);
            $this->sendBalanceChangeNotification($newBalance->balance);
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        $user = $transaction->actor;
        $latestBalance = $user->balance()?->balance ?? 0;

        if ($transaction->type == TransactionTypeEnum::INCOME->value) {
            $balance = $latestBalance - $transaction->amount;
            $note = "[DELETED] " . $transaction->description ?? "";
        } elseif ($transaction->type == TransactionTypeEnum::OUTCOME->value) {
            $balance = $latestBalance + $transaction->amount;
            $note = "[DELETED] " . $transaction->description ?? "";
        }

        if (isset($balance, $note)) {
            $newBalance = Balance::create([
                'balance'          => $balance,
                'note'             => $note,
                'balanceable_type' => User::class,
                'balanceable_id'   => $user->id
            ]);
            $this->sendBalanceChangeNotification($newBalance->balance);
        }
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }

    private function sendBalanceChangeNotification($balance): void
    {
        FirebaseServices::make()
            ->setData([
                'balance' => $balance
            ])->setMethod(FirebaseServices::ByRole)
            ->setRole(RolesPermissionEnum::ADMIN['role'])
            ->setNotification(BalanceChangeNotification::class)
            ->send();
    }
}
