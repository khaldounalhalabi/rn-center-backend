<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class SpecialityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->firstName(),
            'description' => fake()->unique()->text(),
            'tags' => fake()->unique()->text(),
        ];
    }


    public function withClinics($count = 1): SpecialityFactory
    {
        return $this->has(Clinic::factory($count));
    }
}
