<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VacationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::role(collect(["secretary", "doctor"]))
            ->get();

        $vacations = [
            [
                'from' => now()->subDays(15),
                'to' => now()->subDays(12),
                'reason' => 'إجازة مرضية بسبب نزلة برد شديدة',
                'status' => 'approved',
            ],
            [
                'from' => now()->addDays(5),
                'to' => now()->addDays(10),
                'reason' => 'سفر لزيارة العائلة خارج دمشق',
                'status' => 'draft',
            ],
            [
                'from' => now()->subDays(30),
                'to' => now()->subDays(25),
                'reason' => 'إجازة لحضور مناسبة عائلية',
                'status' => 'approved',
            ],
            [
                'from' => now()->subDays(20),
                'to' => now()->subDays(18),
                'reason' => 'طلب إجازة خاصة بدون راتب',
                'status' => 'rejected',
            ],
            [
                'from' => now()->subDays(7),
                'to' => now()->subDays(3),
                'reason' => 'إجازة دراسية للتحضير للامتحانات الجامعية',
                'status' => 'approved',
            ],
            [
                'from' => now()->addDays(20),
                'to' => now()->addDays(25),
                'reason' => 'طلب إجازة سفر سياحية',
                'status' => 'cancelled',
                'cancellation_reason' => 'إلغاء الرحلة لظروف طارئة',
            ],
            [
                'from' => now()->addDays(12),
                'to' => now()->addDays(14),
                'reason' => 'إجازة قصيرة لظرف عائلي',
                'status' => 'draft',
            ],
            [
                'from' => now()->subDays(40),
                'to' => now()->subDays(35),
                'reason' => 'إجازة سنوية مستحقة',
                'status' => 'approved',
            ],
            [
                'from' => now()->subDays(3),
                'to' => now()->addDays(2),
                'reason' => 'إجازة بسبب وعكة صحية',
                'status' => 'approved',
            ],
            [
                'from' => now()->subDays(50),
                'to' => now()->subDays(45),
                'reason' => 'إجازة لمرافقة أحد أفراد العائلة في المستشفى',
                'status' => 'approved',
            ],
        ];

        foreach ($vacations as &$vacation) {
            $vacation['user_id'] = $users->random()->id;
            $vacation['cancellation_reason'] = $vacation['cancellation_reason'] ?? null;
            $vacation['created_at'] = now();
            $vacation['updated_at'] = now();
        }

        DB::table('vacations')->insert($vacations);
    }
}
