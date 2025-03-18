<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'schedulable_id' => Clinic::inRandomOrder()->first()->id,
            'schedulable_type' => Clinic::class,
            'day_of_week' => strtolower(fake()->dayOfWeek),
            'start_time' => fake()->time('H:i'),
            'end_time' => fake()->time('H:i'),
        ];
    }

    public function clinic(): ScheduleFactory
    {
        return $this->state([
            'schedulable_id' => Clinic::factory(),
            'schedulable_type' => Clinic::class,
        ]);
    }
}
