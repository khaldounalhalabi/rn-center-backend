<?php

namespace Database\Factories;

use App\Models\Formula;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class FormulaSegmentFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word,
            'segment' => fake()->randomNumber(2) . '-' . fake()->randomNumber(1),
            'formula_id' => Formula::factory(),
        ];
    }
}
