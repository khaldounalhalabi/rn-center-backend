<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class BalanceFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'balanceable_type' => fake()->word(),
            'balanceable_id' => fake()->numberBetween(1, 2000),
            'balance' => fake()->randomFloat(1, 1, 100),
        ];
    }
}
