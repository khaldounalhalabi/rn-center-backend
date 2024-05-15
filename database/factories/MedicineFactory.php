<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class MedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'description' => fake()->text(),
            'clinic_id' => Clinic::factory()->withSchedules(),
        ];
    }

    public function withPrescriptions($count = 1): MedicineFactory
    {
        return $this->has(Prescription::factory($count));
    }
}
