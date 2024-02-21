<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mother_full_name' => fake()->sentence(),
            'medical_condition' => fake()->text(),
            'user_id' => \App\Models\User::factory() ,

        ];
    }





}
