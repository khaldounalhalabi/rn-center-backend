<?php

namespace Database\Seeders;

use App\Models\Clinic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clinics = Clinic::all();

        $services = [
            // General Consultation
            [
                'name' => 'استشارة طبية عامة',
                'approximate_duration' => 20,
                'price' => 100.0000,
                'description' => 'مراجعة عامة وفحص سريري شامل.',
                'service_category_id' => 1,
                'clinic_id' => $clinics->random()->id
            ],

            // Pediatrics
            [
                'name' => 'متابعة نمو الطفل',
                'approximate_duration' => 30,
                'price' => 120.0000,
                'description' => 'قياس الوزن والطول ومتابعة التطور الصحي.',
                'service_category_id' => 2,
                'clinic_id' => $clinics->random()->id
            ],
            [
                'name' => 'تطعيم الأطفال',
                'approximate_duration' => 15,
                'price' => 80.0000,
                'description' => 'تقديم اللقاحات الأساسية حسب جدول التطعيم.',
                'service_category_id' => 2,
                'clinic_id' => $clinics->random()->id
            ],

            // Cardiology
            [
                'name' => 'تخطيط القلب ECG',
                'approximate_duration' => 20,
                'price' => 200.0000,
                'description' => 'تسجيل النشاط الكهربائي للقلب.',
                'service_category_id' => 3,
                'clinic_id' => $clinics->random()->id
            ],
            [
                'name' => 'إيكو قلب (Echo)',
                'approximate_duration' => 30,
                'price' => 400.0000,
                'description' => 'تصوير القلب بالموجات فوق الصوتية.',
                'service_category_id' => 3,
                'clinic_id' => $clinics->random()->id
            ],

            // Dermatology
            [
                'name' => 'علاج حب الشباب',
                'approximate_duration' => 25,
                'price' => 150.0000,
                'description' => 'خطة علاجية مخصصة لحب الشباب.',
                'service_category_id' => 4,
                'clinic_id' => $clinics->random()->id
            ],
            [
                'name' => 'إزالة الثآليل بالليزر',
                'approximate_duration' => 30,
                'price' => 250.0000,
                'description' => 'إزالة آمنة ودقيقة للثآليل باستخدام الليزر.',
                'service_category_id' => 4,
                'clinic_id' => $clinics->random()->id
            ],

            // Orthopedics
            [
                'name' => 'علاج آلام المفاصل',
                'approximate_duration' => 30,
                'price' => 180.0000,
                'description' => 'جلسة علاجية لتخفيف آلام المفاصل.',
                'service_category_id' => 5,
                'clinic_id' => $clinics->random()->id
            ],
            [
                'name' => 'علاج إصابات الملاعب',
                'approximate_duration' => 40,
                'price' => 250.0000,
                'description' => 'تشخيص وعلاج الإصابات الرياضية.',
                'service_category_id' => 5,
                'clinic_id' => $clinics->random()->id
            ],

            // Gynecology & Obstetrics
            [
                'name' => 'متابعة الحمل',
                'approximate_duration' => 30,
                'price' => 200.0000,
                'description' => 'متابعة صحة الأم والجنين خلال فترة الحمل.',
                'service_category_id' => 7,
                'clinic_id' => $clinics->random()->id
            ],
            [
                'name' => 'تصوير الجنين بالسونار',
                'approximate_duration' => 20,
                'price' => 300.0000,
                'description' => 'فحص بالموجات فوق الصوتية لمتابعة نمو الجنين.',
                'service_category_id' => 7,
                'clinic_id' => $clinics->random()->id
            ],

            // Dentistry
            [
                'name' => 'تنظيف الأسنان',
                'approximate_duration' => 30,
                'price' => 150.0000,
                'description' => 'إزالة الترسبات الجيرية وتلميع الأسنان.',
                'service_category_id' => 10,
                'clinic_id' => $clinics->random()->id
            ],
            [
                'name' => 'حشو الأسنان',
                'approximate_duration' => 45,
                'price' => 300.0000,
                'description' => 'علاج تسوس الأسنان وحشوها بالمواد المناسبة.',
                'service_category_id' => 10,
                'clinic_id' => $clinics->random()->id
            ],
        ];

        DB::table('services')->insert($services);
    }
}
