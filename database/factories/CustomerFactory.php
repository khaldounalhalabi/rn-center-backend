<?php

namespace Database\Factories;

use App\Enums\BloodGroupEnum;
use App\Enums\RolesPermissionEnum;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $healthStatusExamples = [
            'المريض بصحة جيدة، لا توجد أمراض مزمنة.',
            'المريض يعاني من ارتفاع ضغط الدم الخفيف، يراقب بانتظام.',
            'المريض مصاب بالسكري من النوع الثاني، يتناول أدوية يومية.',
            'المريض يعاني من حساسية تجاه الغلوتين.',
            'المريض يعاني من آلام متكررة في المفاصل، ينصح بالعلاج الطبيعي.',
            'المريض لديه تاريخ مرضي لارتفاع الكولسترول.',
            'المريض خضع لعملية جراحية بسيطة قبل 6 أشهر.',
        ];

        $otherDataExamples = [
            [
                ['key' => 'الوزن', 'value' => '70 كغ'],
                ['key' => 'ضغط الدم', 'value' => '120/80 ملم زئبق'],
                ['key' => 'درجة الحرارة', 'value' => '36.7 °م'],
            ],
            [
                ['key' => 'الوزن', 'value' => '82 كغ'],
                ['key' => 'ضغط الدم', 'value' => '135/85 ملم زئبق'],
                ['key' => 'درجة الحرارة', 'value' => '37.1 °م'],
            ],
            [
                ['key' => 'الوزن', 'value' => '65 كغ'],
                ['key' => 'ضغط الدم', 'value' => '110/70 ملم زئبق'],
                ['key' => 'درجة الحرارة', 'value' => '36.5 °م'],
            ],
        ];

        return [
            'user_id' => User::factory()->create()->assignRole(RolesPermissionEnum::CUSTOMER['role'])->id,
            'birth_date' => fake()->date(),
            'blood_group' => fake()->randomElement(BloodGroupEnum::getAllValues()),
            'health_status' => fake()->randomElement($healthStatusExamples),
            'notes' => fake()->randomElement($healthStatusExamples),
            'other_data' => fake()->randomElement($otherDataExamples),
        ];
    }

    public function configure(): CustomerFactory
    {
        return $this->afterCreating(function (Customer $customer) {
            fakeImage($customer, true);
        });
    }

    public function allRelations(): CustomerFactory
    {
        return $this->withAppointments();
    }

    public function withAppointments($count = 1): CustomerFactory
    {
        return $this->has(Appointment::factory($count));
    }

    public function withPrescriptions($count = 1): CustomerFactory
    {
        return $this->has(Prescription::factory($count));
    }

    public function withMedicalRecords($count = 1): CustomerFactory
    {
        return $this->has(MedicalRecord::factory($count));
    }
}
