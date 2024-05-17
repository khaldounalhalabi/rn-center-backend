<?php

namespace Database\Factories;

use App\Models\Hospital;
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
        $userChance = fake()->boolean;
        return [
            'phone' => "07" . fake()->randomNumber(9 , true),
            'phoneable_id' => $userChance ? User::factory()->create()->id : Hospital::factory()->create()->id,
            'phoneable_type' => $userChance ? User::class : Hospital::class,
        ];
    }
}
