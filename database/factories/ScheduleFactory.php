<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $relChance = fake()->boolean;
        return [
            'clinic_id' => $relChance ? Clinic::factory() : null,
            'day_of_week' => fake()->dayOfWeek,
            'start_time' => fake()->time('H:i'),
            'end_time' => fake()->time('H:i'),
            'hospital_id' => !$relChance ? Hospital::factory() : null,
        ];
    }


}
