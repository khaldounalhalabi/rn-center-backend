<?php

namespace Database\Seeders;

use App\Enums\AssetTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $assets = [
            [
                'name' => 'حاسوب محمول Dell',
                'serial_number' => 'DL2025-001',
                'type' => AssetTypeEnum::ASSET->value,
                'quantity' => 1,
                'purchase_date' => Carbon::now()->subMonths(12),
                'quantity_unit' => 'قطعة',
            ],
            [
                'name' => 'أقلام حبر جاف',
                'serial_number' => null,
                'type' => AssetTypeEnum::CONSUMABLE->value,
                'quantity' => 500,
                'purchase_date' => Carbon::now()->subMonths(1),
                'quantity_unit' => 'قطعة',
            ],
            [
                'name' => 'ماعون ورق',
                'serial_number' => null,
                'type' => AssetTypeEnum::CONSUMABLE->value,
                'quantity' => 100,
                'purchase_date' => Carbon::now()->subMonths(1),
                'quantity_unit' => 'ماعون',
            ],
            [
                'name' => 'كفوف استعمال مرة واحدة',
                'serial_number' => null,
                'type' => AssetTypeEnum::CONSUMABLE->value,
                'quantity' => 200,
                'purchase_date' => Carbon::now()->subMonths(1),
                'quantity_unit' => 'علبة',
            ],
            [
                'name' => 'ملعقة اللسان',
                'serial_number' => null,
                'type' => AssetTypeEnum::CONSUMABLE->value,
                'quantity' => 150,
                'purchase_date' => Carbon::now()->subMonths(1),
                'quantity_unit' => 'علبة',
            ],
            // معدات طبية
            [
                'name' => 'جهاز قياس ضغط الدم Omron',
                'serial_number' => 'BP-OM-2025-01',
                'type' => AssetTypeEnum::ACCESSORIES->value,
                'quantity' => 10,
                'purchase_date' => Carbon::now()->subMonths(6),
                'quantity_unit' => 'قطعة',
            ],
            [
                'name' => 'جهاز قياس سكر الدم Accu-Chek',
                'serial_number' => 'GL-AC-2025-02',
                'type' => AssetTypeEnum::ACCESSORIES->value,
                'quantity' => 15,
                'purchase_date' => Carbon::now()->subMonths(4),
                'quantity_unit' => 'قطعة',
            ],
            [
                'name' => 'سماعة طبية Littmann',
                'serial_number' => 'ST-LIT-2025-03',
                'type' => AssetTypeEnum::ACCESSORIES->value,
                'quantity' => 20,
                'purchase_date' => Carbon::now()->subMonths(3),
                'quantity_unit' => 'قطعة',
            ],
            [
                'name' => 'جهاز قياس حرارة رقمي',
                'serial_number' => 'TH-DIG-2025-04',
                'type' => AssetTypeEnum::ACCESSORIES->value,
                'quantity' => 25,
                'purchase_date' => Carbon::now()->subMonths(2),
                'quantity_unit' => 'قطعة',
            ],
            [
                'name' => 'جهاز مراقبة نبض وأكسجين Pulse Oximeter',
                'serial_number' => 'PO-2025-05',
                'type' => AssetTypeEnum::ACCESSORIES->value,
                'quantity' => 12,
                'purchase_date' => Carbon::now()->subMonths(5),
                'quantity_unit' => 'قطعة',
            ],
            [
                'name' => 'أجهزة تحليل دم صغيرة (Portable Hematology Analyzer)',
                'serial_number' => 'HA-2025-06',
                'type' => AssetTypeEnum::ACCESSORIES->value,
                'quantity' => 5,
                'purchase_date' => Carbon::now()->subMonths(8),
                'quantity_unit' => 'قطعة',
            ],
            [
                'name' => 'هاتف سامسونغ J7',
                'serial_number' => 'SA-2025-06',
                'type' => AssetTypeEnum::ASSET->value,
                'quantity' => 1,
                'purchase_date' => Carbon::now()->subMonths(8),
                'quantity_unit' => 'قطعة',
            ],
        ];

        foreach ($assets as &$asset) {
            $asset['created_at'] = now();
            $asset['updated_at'] = now();
        }

        DB::table('assets')->insert($assets);
    }
}
