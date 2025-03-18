<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\ClinicJoinRequest;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class CityFactory extends Factory
{
    use Translations;

    /**
     * Define the model's default state.
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
        return $this->has(Address::factory($count));
    }

    public function withClinicJoinRequests($count = 1): CityFactory
    {
        return $this->has(ClinicJoinRequest::factory($count));
    }
}
