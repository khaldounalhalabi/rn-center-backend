<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\ServiceCategory;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ServiceFactory extends Factory
{
    use Translations;

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'                 => $this->fakeTranslation('word'),
            'approximate_duration' => fake()->numberBetween(1, 2000),
            'service_category_id'  => ServiceCategory::factory(),
            'price'                => fake()->randomFloat(2, 0, 1000),
            'status'               => fake()->numberBetween(1, 2000),
            'description'          => $this->fakeTranslation('word'),
            'clinic_id'            => Clinic::factory()->withSchedules(),
        ];
    }

    public function withAppointments($count = 1): ServiceFactory
    {
        return $this->has(\App\Models\Appointment::factory($count));
    }
}
