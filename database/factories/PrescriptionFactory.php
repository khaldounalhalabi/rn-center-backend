<?php

namespace Database\Factories;

use App\Models\MedicinePrescription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class PrescriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clinic_id' => \App\Models\Clinic::factory(),
            'customer_id' => \App\Models\Customer::factory(),
            'physical_information' => json_encode([fake()->word() => fake()->word()]),
            'problem_description' => fake()->unique()->text(),
            'test' => fake()->unique()->text(),
            'next_visit' => fake()->unique()->word(),

        ];
    }

    public function allRelations()
    {
        return $this->withMedicineData();
    }

    public function withMedicines($count = 1): PrescriptionFactory
    {
        return $this->has(\App\Models\Medicine::factory($count));
    }

    public function withMedicineData($count = 1): PrescriptionFactory
    {
        return $this->has(MedicinePrescription::factory($count) , 'medicinesData');
    }
}
