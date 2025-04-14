<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class HolidayFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from' => now()->subDays(fake()->numberBetween(30, 60)),
            'to' => now()->addDays(fake()->numberBetween(30, 60)),
            'reason' => fake()->text(),
        ];
    }
}
