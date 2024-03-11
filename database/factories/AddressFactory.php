<?php

namespace Database\Factories;

use App\Models\Hospital;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userChance = fake()->boolean;
        return [
            'name' => fake()->unique()->firstName(),
            'city' => fake()->unique()->city(),
            'lat' => fake()->unique()->text(),
            'lng' => fake()->unique()->text(),
            'country' => fake()->unique()->country(),
            'addressable_id' => $userChance ? User::factory() : Hospital::factory(),
            'addressable_type' => $userChance ? User::class : Hospital::class,
        ];
    }
}
