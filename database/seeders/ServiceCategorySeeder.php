<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'الاستشارة العامة'],
            ['name' => 'طب الأطفال'],
            ['name' => 'أمراض القلب'],
            ['name' => 'الأمراض الجلدية'],
            ['name' => 'جراحة العظام'],
            ['name' => 'طب الأعصاب'],
            ['name' => 'أمراض النساء والتوليد'],
            ['name' => 'طب العيون'],
            ['name' => 'الأنف والأذن والحنجرة'],
            ['name' => 'طب الأسنان'],
            ['name' => 'الأشعة والتصوير الطبي'],
            ['name' => 'المختبرات الطبية'],
            ['name' => 'العلاج الطبيعي وإعادة التأهيل'],
            ['name' => 'الطب النفسي والصحة النفسية'],
            ['name' => 'التغذية والحمية'],
            ['name' => 'قسم الطوارئ'],
            ['name' => 'الجراحة'],
            ['name' => 'الأورام'],
            ['name' => 'أمراض الكلى وغسيل الكلى'],
            ['name' => 'أمراض الجهاز التنفسي'],
        ];

        DB::table('service_categories')->insert($categories);
    }
}
