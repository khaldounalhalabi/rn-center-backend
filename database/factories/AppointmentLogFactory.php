<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class AppointmentLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'appointment_id' => \App\Models\Appointment::factory() ,
            'cancellation_reason' => fake()->unique()->text(),
            'status' => fake()->unique()->word(),
            'actor_id' => \App\Models\Actor::factory() ,
            'affected_id' => \App\Models\Affected::factory() ,
            'happen_in' => fake()->unique()->dateTime(),

        ];
    }





}
