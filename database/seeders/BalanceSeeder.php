<?php

namespace Database\Seeders;

use App\Enums\ClinicTransactionStatusEnum;
use App\Models\Balance;
use App\Models\Clinic;
use App\Models\ClinicTransaction;
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
        ClinicTransaction::orderBy('date')->get()->each(function (ClinicTransaction $transaction) {
            if ($transaction->status == ClinicTransactionStatusEnum::DONE->value) {
                if ($transaction->isPlus()) {
                    $latestBalance = $transaction->clinic?->balance?->balance ?? 0;
                    Balance::create([
                        'balance' => $latestBalance + $transaction->amount,
                        'balanceable_id' => $transaction->clinic_id,
                        'balanceable_type' => Clinic::class,
                    ]);
                }
                if ($transaction->isMinus()) {
                    $latestBalance = $transaction->clinic?->balance?->balance ?? 0;
                    Balance::create([
                        'balance' => $latestBalance - $transaction->amount,
                        'balanceable_id' => $transaction->clinic_id,
                        'balanceable_type' => Clinic::class,
                    ]);
                }
            }
        });

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
