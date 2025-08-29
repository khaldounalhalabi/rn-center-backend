<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Clinic;
use Carbon\Carbon;

class MedicalRecordSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $clinics = Clinic::all();

        $records = [
            [
                'summary' => 'زيارة روتينية بعدوى تنفسية بسيطة.',
                'diagnosis' => 'نزلة برد عادية',
                'treatment' => 'راحة، شرب سوائل دافئة، مسكن خفيف عند الحاجة',
                'allergies' => 'لا توجد',
                'notes' => 'المريض يحتاج متابعة في حال استمرار السعال لأكثر من أسبوع',
            ],
            [
                'summary' => 'مراجعة لإصابة طفيفة في الركبة بعد سقوط بسيط.',
                'diagnosis' => 'ارتجاج في الركبة',
                'treatment' => 'تطبيق كمادات باردة، مسكنات خفيفة، رفع الساق عند الراحة',
                'allergies' => 'لا توجد',
                'notes' => 'مراجعة بعد 5 أيام إذا استمر الألم أو تورم',
            ],
            [
                'summary' => 'زيارة متابعة لارتفاع ضغط الدم.',
                'diagnosis' => 'ارتفاع ضغط الدم الخفيف',
                'treatment' => 'دواء ضغط الدم (amlodipine 5mg يومياً)، مراقبة الضغط أسبوعياً',
                'allergies' => 'حساسية من الأسبرين',
                'notes' => 'تنبيه المريض لتقليل الملح في الغذاء',
            ],
            [
                'summary' => 'زيارة بسبب صداع متكرر.',
                'diagnosis' => 'صداع توتري',
                'treatment' => 'مسكنات خفيفة، نصائح لتقليل التوتر، تمارين استرخاء',
                'allergies' => 'لا توجد',
                'notes' => 'تجنب الكافيين الزائد والمجهود البدني الشديد',
            ],
            [
                'summary' => 'مراجعة طفيفة بعد التهاب الحلق.',
                'diagnosis' => 'التهاب حلق خفيف',
                'treatment' => 'غسولات فموية، مسكن خفيف عند الحاجة',
                'allergies' => 'لا توجد',
                'notes' => 'المريض يحتاج مراقبة حرارة الجسم خلال الأيام القادمة',
            ],
        ];

        foreach ($records as &$record) {
            $record['customer_id'] = $customers->random()->id;
            $record['clinic_id'] = $clinics->random()->id;
            $record['created_at'] = Carbon::now()->subDays(rand(1, 30));
            $record['updated_at'] = Carbon::now();
        }

        DB::table('medical_records')->insert($records);
    }
}
