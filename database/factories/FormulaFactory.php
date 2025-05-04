<?php

namespace Database\Factories;

use App\Models\Formula;
use App\Models\FormulaSegment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class FormulaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $formula = fake()->randomNumber(2) . '-' . fake()->randomNumber(1) . '+' . fake()->randomNumber(3) . '*' . fake()->randomNumber(2);

        return [
            'name' => fake()->firstName(),
            'formula' => $formula,
            'template' => $formula,
        ];
    }

    public function withUsers($count = 1): FormulaFactory
    {
        return $this->has(User::factory($count));
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Formula $formula) {
            $segments = $formula->splitSegments();
            foreach ($segments as $segment) {
                FormulaSegment::create([
                    'segment' => $segment,
                    'name' => fake()->word(),
                    'formula_id' => $formula->id,
                ]);
            }
        });
    }

    public function withFormulaSegments($count = 1): FormulaFactory
    {
        return $this->has(FormulaSegment::factory($count));
    }
}
