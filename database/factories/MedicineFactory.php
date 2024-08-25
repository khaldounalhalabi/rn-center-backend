<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class MedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => fake()->firstName(),
            'description' => fake()->text(),
            'clinic_id'   => Clinic::inRandomOrder()->first()->id,
        ];
    }

    public function withPrescriptions($count = 1): MedicineFactory
    {
        return $this->has(Prescription::factory($count));
    }
}
