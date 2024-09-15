<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ClinicJoinRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_name'  => fake()->word(),
            'clinic_name'  => fake()->word(),
            'phone_number' => fake()->phoneNumber(),
            'city_id'      => City::inRandomOrder()->first()->id,
        ];
    }
}
