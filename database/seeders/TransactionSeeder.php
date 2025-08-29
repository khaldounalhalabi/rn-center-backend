<?php

namespace Database\Seeders;

use App\Enums\TransactionTypeEnum;
use App\Models\Balance;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $transactions = [
            [
                'type' => 'income',
                'amount' => 150000,
                'description' => 'تبرع من جمعية الهلال الأحمر لدعم المركز',
                'date' => Carbon::now()->subDays(2),
            ],
            [
                'type' => 'income',
                'amount' => 80000,
                'description' => 'مساهمة من متبرع فردي لدعم شراء الأدوية',
                'date' => Carbon::now()->subDays(4),
            ],
            [
                'type' => 'income',
                'amount' => 200000,
                'description' => 'تمويل حكومي لتوسيع قسم الطوارئ',
                'date' => Carbon::now()->subDays(7),
            ],
            [
                'type' => 'outcome',
                'amount' => 60000,
                'description' => 'شراء أدوية أساسية للمستودع',
                'date' => Carbon::now()->subDays(3),
            ],
            [
                'type' => 'outcome',
                'amount' => 45000,
                'description' => 'شراء أدوات تعقيم ومواد تنظيف للمركز',
                'date' => Carbon::now()->subDays(5),
            ],
            [
                'type' => 'outcome',
                'amount' => 120000,
                'description' => 'تجهيز أجهزة طبية جديدة لقسم الأشعة',
                'date' => Carbon::now()->subDays(8),
            ],
            [
                'type' => 'income',
                'amount' => 50000,
                'description' => 'تبرع من منظمة خيرية لدعم الأطفال المرضى',
                'date' => Carbon::now()->subDays(9),
            ],
            [
                'type' => 'outcome',
                'amount' => 30000,
                'description' => 'صيانة مولد الكهرباء في المركز',
                'date' => Carbon::now()->subDays(10),
            ],
            [
                'type' => 'income',
                'amount' => 100000,
                'description' => 'تبرع من رجال أعمال لدعم العيادات المجانية',
                'date' => Carbon::now()->subDays(12),
            ],
        ];

        $balance = 0;
        $balances = [];
        foreach ($transactions as &$transaction) {
            $transaction['actor_id'] = 1;
            $transaction['appointment_id'] = null;
            $transaction['payrun_id'] = null;
            $transaction['created_at'] = now();
            $transaction['updated_at'] = now();
            if ($transaction['type'] == TransactionTypeEnum::INCOME->value) {
                $balance += $transaction['amount'];
            } else {
                $balance -= $transaction['amount'];
            }
            $balances[] = [
                'balance' => $balance,
                'created_at' => $transaction['date'],
                'updated_at' => $transaction['date'],
            ];
        }

        DB::table('transactions')->insert($transactions);
        Balance::insert($balances);
    }
}
