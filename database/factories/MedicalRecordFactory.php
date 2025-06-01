<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class MedicalRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'clinic_id' => Clinic::inRandomOrder()->first()?->id ?? Clinic::factory()->create()->id,
            'summary' => fake()->text(),
            'diagnosis' => fake()->text(),
            'treatment' => fake()->text(),
            'allergies' => fake()->text(),
            'notes' => fake()->text(),
        ];
    }
}
