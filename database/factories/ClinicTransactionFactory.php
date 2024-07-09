<?php

namespace Database\Factories;

use App\Enums\ClinicTransactionStatusEnum;
use App\Enums\ClinicTransactionTypeEnum;
use App\Models\Appointment;
use App\Models\AppointmentDeduction;
use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ClinicTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clinic = Clinic::first() ?? Clinic::factory()->create();
        $appointment = Appointment::where('clinic_id', $clinic->id)->inRandomOrder()->first();

        return [
            'amount'         => fake()->randomNumber(1, 10),
            'appointment_id' => $appointment?->id,
            'type'           => ClinicTransactionTypeEnum::INCOME->value,
            'clinic_id'      => $clinic->id,
            'notes'          => fake()->text(),
            'status'         => ClinicTransactionStatusEnum::PENDING->value,
        ];
    }

    public function withAppointmentDeduction(): ClinicTransactionFactory
    {
        return $this->has(AppointmentDeduction::factory());
    }
}
