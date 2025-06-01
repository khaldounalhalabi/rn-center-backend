<?php

namespace Database\Factories;

use App\Enums\BloodGroupEnum;
use App\Enums\RolesPermissionEnum;
use App\Models\Appointment;
use App\Models\MedicalRecord;
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
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->assignRole(RolesPermissionEnum::CUSTOMER['role'])->id,
            'birth_date' => fake()->date(),
            'blood_group' => fake()->randomElement(BloodGroupEnum::getAllValues()),
            'health_status' => fake()->text(),
            'notes' => fake()->text(),
            'other_data' => [
                [
                    'key' => fake()->word(),
                    'value' => fake()->text(),
                ],
            ],
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

    public function withMedicalRecords($count = 1): CustomerFactory
    {
        return $this->has(MedicalRecord::factory($count));
    }
}
