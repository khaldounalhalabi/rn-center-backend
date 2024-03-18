<?php

namespace Database\Factories;

use App\Models\Hospital;
use App\Models\User;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class AddressFactory extends Factory
{
    use Translations;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userChance = fake()->boolean;
        return [
            'name' => $this->fakeTranslation('address'),
            'city' => $this->fakeTranslation('city'),
            'lat' => fake()->unique()->text(),
            'lng' => fake()->unique()->text(),
            'country' => fake()->unique()->country(),
            'addressable_id' => $userChance ? User::factory() : Hospital::factory(),
            'addressable_type' => $userChance ? User::class : Hospital::class,
        ];
    }
}
