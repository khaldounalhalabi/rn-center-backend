<?php

namespace Database\Factories;

use App\Enums\AppointmentDeductionStatusEnum;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ClinicTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class AppointmentDeductionFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clinic = Clinic::whereHas('appointments')->inRandomOrder()->first();
        $appointment = Appointment::where('clinic_id' , $clinic->id)->first();
        return [
            'amount'                => fake()->randomFloat(1, 1, 100),
            'status'                => fake()->randomElement(AppointmentDeductionStatusEnum::getAllValues()),
            'clinic_transaction_id' => ClinicTransaction::factory(),
            'appointment_id'        => $appointment?->id,
            'clinic_id'             => $clinic?->id,
            'date'                  => fake()->dateTimeBetween('-5 days' , '+5 days'),
        ];
    }
}
