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
            'schedulable_id' => $relChance ? Clinic::factory()->withSchedules() : Hospital::factory(),
            'schedulable_type' => $relChance ? Clinic::class : Hospital::class,
            'day_of_week' => strtolower(fake()->dayOfWeek),
            'start_time' => fake()->time('H:i'),
            'end_time' => fake()->time('H:i'),
        ];
    }

    public function clinic(): ScheduleFactory
    {
        return $this->state([
            'schedulable_id' => Clinic::factory(),
            'schedulable_type' => Clinic::class
        ]);
    }

    public function hospital(): ScheduleFactory
    {
        return $this->state([
            'schedulable_id' => Hospital::factory(),
            'schedulable_type' => Hospital::class
        ]);
    }
}
