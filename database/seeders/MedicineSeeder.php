<?php

namespace Database\Seeders;

use App\Enums\MedicineStatusEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            [
                'name' => 'باراسيتامول 500mg',
                'description' => 'مسكن للآلام وخافض للحرارة.',
                'status' => MedicineStatusEnum::EXISTS->value,
                'barcode' => '6254000011111',
                'quantity' => 120,
            ],
            [
                'name' => 'إيبوبروفين 400mg',
                'description' => 'مضاد التهاب غير ستيروئيدي لتسكين الألم وخفض الحرارة.',
                'status' => MedicineStatusEnum::EXISTS->value,
                'barcode' => '6254000011112',
                'quantity' => 75,
            ],
            [
                'name' => 'أموكسيسيلين 500mg',
                'description' => 'مضاد حيوي واسع الطيف لعلاج الالتهابات البكتيرية.',
                'status' => MedicineStatusEnum::EXISTS->value,
                'barcode' => '6254000011113',
                'quantity' => 200,
            ],
            [
                'name' => 'فيتامين C 1000mg',
                'description' => 'مكمل غذائي لدعم المناعة.',
                'status' => MedicineStatusEnum::EXISTS->value,
                'barcode' => '6254000011114',
                'quantity' => 60,
            ],
            [
                'name' => 'ميفامير (Metformin 850mg)',
                'description' => 'دواء للسكري من النوع الثاني.',
                'status' => MedicineStatusEnum::EXISTS->value,
                'barcode' => '6254000011115',
                'quantity' => 90,
            ],
            [
                'name' => 'أوميبرازول 20mg',
                'description' => 'لعلاج قرحة المعدة والارتجاع المريئي.',
                'status' => MedicineStatusEnum::EXISTS->value,
                'barcode' => '6254000011116',
                'quantity' => 150,
            ],
            [
                'name' => 'سيتريزين 10mg',
                'description' => 'مضاد هيستامين لعلاج الحساسية.',
                'status' => MedicineStatusEnum::EXISTS->value,
                'barcode' => '6254000011117',
                'quantity' => 50,
            ],
            [
                'name' => 'أسبرين 100mg',
                'description' => 'مضاد صفائح دموية للوقاية من الجلطات.',
                'status' => 'out_of_stock',
                'barcode' => '6254000011118',
                'quantity' => 0,
            ],
            [
                'name' => 'لورازيبام 1mg',
                'description' => 'مهدئ للقلق واضطرابات النوم.',
                'status' => MedicineStatusEnum::EXISTS->value,
                'barcode' => '6254000011119',
                'quantity' => 40,
            ],
            [
                'name' => 'ديكساميثازون 4mg',
                'description' => 'كورتيكوستيرويد مضاد للالتهاب.',
                'status' => MedicineStatusEnum::EXISTS->value,
                'barcode' => '6254000011120',
                'quantity' => 65,
            ],
        ];

        DB::table('medicines')->insert($medicines);
    }
}
