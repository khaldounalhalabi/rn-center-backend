<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ServiceCategory;
use App\Serializers\Translatable;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ServiceFactory extends Factory
{

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => Translatable::fake(),
            'approximate_duration' => fake()->numberBetween(1, 2000),
            'service_category_id' => ServiceCategory::inRandomOrder()->first()->id,
            'price' => fake()->randomFloat(2, 0, 1000),
            'status' => fake()->numberBetween(1, 2000),
            'description' => Translatable::fake(),
            'clinic_id' => Clinic::inRandomOrder()->first()?->id ?? Clinic::factory()->create()->id,
        ];
    }

    public function withAppointments($count = 1): ServiceFactory
    {
        return $this->has(Appointment::factory($count));
    }
}
