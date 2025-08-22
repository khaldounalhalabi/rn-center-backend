<?php

namespace Database\Seeders;

use App\Models\Balance;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class BalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::orderBy('date')->get()->each(function (Transaction $transaction) {
            if ($transaction->isIncome()) {
                $latestBalance = Balance::latest()->first()?->balance ?? 0;
                Balance::create([
                    'balance' => $latestBalance + $transaction->amount,
                ]);
            }
            if ($transaction->isOutcome()) {
                $latestBalance = Balance::latest()->first()?->balance ?? 0;
                Balance::create([
                    'balance' => $latestBalance - $transaction->amount,
                ]);
            }
        });
    }
}
