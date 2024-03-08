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
            'schedulable_id' => $relChance ? Clinic::factory() : Hospital::factory(),
            'schedulable_type' => $relChance ? Clinic::class : Hospital::class,
            'day_of_week' => fake()->dayOfWeek,
            'start_time' => fake()->time('H:i'),
            'end_time' => fake()->time('H:i'),
        ];
    }


}
