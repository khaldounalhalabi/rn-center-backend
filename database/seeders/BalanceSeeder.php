<?php

namespace Database\Seeders;

use App\Models\Balance;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class BalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::orderBy('date')->get()->each(function (Transaction $transaction) {
            if ($transaction->isPlus()) {
                $latestBalance = $transaction->actor?->balance()->balance ?? 0;
                Balance::create([
                    'balance' => $latestBalance + $transaction->amount,
                    'balanceable_id' => $transaction->actor_id,
                    'balanceable_type' => User::class,
                ]);
            }
            if ($transaction->isMinus()) {
                $latestBalance = $transaction->actor?->balance()->balance ?? 0;
                Balance::create([
                    'balance' => $latestBalance - $transaction->amount,
                    'balanceable_id' => $transaction->actor_id,
                    'balanceable_type' => User::class,
                ]);
            }
        });
    }
}
