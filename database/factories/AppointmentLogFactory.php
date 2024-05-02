<?php

namespace Database\Factories;

use App\Enums\AppointmentStatusEnum;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\User;
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
            'appointment_id' => Appointment::factory(),
            'cancellation_reason' => fake()->text(),
            'status' => fake()->randomElement(AppointmentStatusEnum::getAllValues()),
            'actor_id' => User::factory(),
            'affected_id' => User::factory(),
            'happen_in' => now(),
        ];
    }
}
