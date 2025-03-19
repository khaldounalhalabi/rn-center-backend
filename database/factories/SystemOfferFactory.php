<?php

namespace Database\Factories;

use App\Enums\OfferTypeEnum;
use App\Models\Appointment;
use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class SystemOfferFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->word(),
            'description' => fake()->text(),
            'type' => fake()->randomElement(OfferTypeEnum::getAllValues()),
            'amount' => fake()->numberBetween(5, 100),
            'allowed_uses' => fake()->numberBetween(1, 50),
            'allow_reuse' => fake()->boolean(),
            'from' => fake()->dateTimeBetween('today', '+5 days'),
            'to' => fake()->dateTimeBetween('+5 days', '+ 10 days'),
        ];
    }

    public function withClinics($count = 1): SystemOfferFactory
    {
        return $this->has(Clinic::factory($count));
    }

    public function withAppointments($count = 1): SystemOfferFactory
    {
        return $this->has(Appointment::factory($count));
    }
}
