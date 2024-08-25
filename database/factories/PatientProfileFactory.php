<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class PatientProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id'       => Customer::inRandomOrder()->first()->id,
            'clinic_id'         => Clinic::inRandomOrder()->first()->id,
            'medical_condition' => fake()->text(),
            'note'              => fake()->text(),
            'other_data'        => json_encode([fake()->word() => fake()->word()]),
        ];
    }
}
