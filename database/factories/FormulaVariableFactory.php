<?php

namespace Database\Factories;

use App\Models\Formula;
use App\Serializers\Translatable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class FormulaVariableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => Translatable::fake(),
            'description' => fake()->text(),
        ];
    }

    public function withFormulas($count = 1): FormulaVariableFactory
    {
        return $this->has(Formula::factory($count));
    }
}
