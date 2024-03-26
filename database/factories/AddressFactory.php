<?php

namespace Database\Factories;

use App\Models\City;
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
            'city_id' => City::inRandomOrder()->first()->id,
            'lat' => fake()->unique()->latitude(),
            'lng' => fake()->unique()->longitude(),
            'country' => fake()->unique()->country(),
            'addressable_id' => $userChance ? User::factory() : Hospital::factory(),
            'addressable_type' => $userChance ? User::class : Hospital::class,
        ];
    }
}
