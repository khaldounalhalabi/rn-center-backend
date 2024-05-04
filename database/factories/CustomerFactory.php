<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'medical_condition' => fake()->text(),
            'user_id' => User::factory()->allRelations(),
        ];
    }

    public function allRelations(): CustomerFactory
    {
        return $this->withAppointments();
    }

    public function withAppointments($count = 1): CustomerFactory
    {
        return $this->has(Appointment::factory($count));
    }

    public function withPrescriptions($count = 1): CustomerFactory
    {
        return $this->has(Prescription::factory($count));
    }
}
