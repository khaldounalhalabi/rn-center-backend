<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Customer;
use App\Models\Medicine;
use App\Models\MedicinePrescription;
use App\Models\Prescription;
use App\Services\AvailableAppointmentTimeService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class PrescriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clinicId = Clinic::inRandomOrder()->first()?->id ?? Clinic::factory()->create()->id;
        $customerId = Customer::inRandomOrder()->first()?->id ?? Customer::factory()->create()->id;
        $appointment = Appointment::factory()->state([
            'clinic_id' => $clinicId,
            'customer_id' => $customerId,
        ])->create();

        $nextVisit = AvailableAppointmentTimeService::make()
            ->getAvailableTimeSlots($clinicId, $appointment->date_time->addDays(fake()->numberBetween(1, 30))
                ->format('Y-m-d'))->first();

        return [
            'clinic_id' => $clinicId,
            'customer_id' => $customerId,
            'appointment_id' => $appointment->id,
            'other_data' => [
                [
                    'key' => fake()->word(),
                    'value' => fake()->text,
                ]
            ],
            'next_visit' => $nextVisit
        ];
    }

    public function allRelations(): PrescriptionFactory
    {
        return $this->withMedicineData();
    }

    public function withMedicineData($count = 1): PrescriptionFactory
    {
        return $this->afterCreating(function (Prescription $prescription) use ($count) {
            MedicinePrescription::factory($count)
                ->state([
                    'prescription_id' => $prescription->id,
                ])->create();
        });
    }

    public function withMedicines($count = 1): PrescriptionFactory
    {
        return $this->has(Medicine::factory($count));
    }
}
