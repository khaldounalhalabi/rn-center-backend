<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class PatientStudyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->word(),
            'patient_uuid' => fake()->word(),
            'customer_id' => Customer::factory(),
            'study_uuid' => fake()->word(),
            'study_uid' => fake()->word(),

        ];
    }
}
