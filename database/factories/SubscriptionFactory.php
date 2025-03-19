<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'description' => fake()->text(),
            'period' => fake()->numberBetween(1, 12),
            'allow_period' => fake()->numberBetween(1, 7),
            'cost' => fake()->randomFloat(2, 0, 15),
        ];
    }

    public function withClinics($count = 1): SubscriptionFactory
    {
        return $this->has(Clinic::factory($count));
    }
}
