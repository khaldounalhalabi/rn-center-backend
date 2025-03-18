<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class PhoneNumberFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'phone' => "07" . fake()->unique()->randomNumber(9, true),
            'phoneable_id' => User::factory()->create()->id,
            'phoneable_type' => User::class,
        ];
    }
}
