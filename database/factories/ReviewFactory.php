<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'customer_id' => Customer::factory(),
            'rate' => fake()->numberBetween(1, 5),
            'review' => fake()->sentence(),
        ];
    }
}
