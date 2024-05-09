<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class CityFactory extends Factory
{
    use \App\Traits\Translations;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->fakeTranslation('word'),
        ];
    }

    public function withAddresses($count = 1): CityFactory
    {
        return $this->has(\App\Models\Address::factory($count));
    }
}
