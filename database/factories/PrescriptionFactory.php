<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Customer;
use App\Models\Medicine;
use App\Models\MedicinePrescription;
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

        $physicalInformation = [
            "High Blood Pressure" => fake()->sentence(5),
            "Diabetic"            => fake()->sentence(5),
            "Food Allergies"      => fake()->sentence(5),
            "Tendency Bleed"      => fake()->sentence(5),
            "Heart Disease"       => fake()->sentence(5),
            "Medical History"     => fake()->sentence(5),
            "Female Pregnancy"    => fake()->sentence(5),
            "Breast Feeding"      => fake()->sentence(5),
            "Current Medication"  => fake()->sentence(5),
            "Surgery"             => fake()->sentence(5),
            "Accident"            => fake()->sentence(5),
            "Others"              => fake()->sentence(5),
            "Pulse Rate"          => fake()->sentence(5),
            "Temperature"         => fake()->sentence(5),
        ];
        return [
            'clinic_id'            => $clinicId,
            'customer_id'          => $customerId,
            'appointment_id'       => Appointment::inRandomOrder()
                    ->where('customer_id', $customerId)
                    ->where('clinic_id', $clinicId)->first()->id ?? Appointment::factory()->create([
                    'clinic_id'   => $clinicId,
                    'customer_id' => $customerId,
                ])->id,
            'physical_information' => json_encode($physicalInformation),
            'problem_description'  => fake()->text(),
            'test'                 => fake()->text(),
            'next_visit'           => "Next Week",
        ];
    }

    public function allRelations(): PrescriptionFactory
    {
        return $this->withMedicineData();
    }

    public function withMedicineData($count = 1): PrescriptionFactory
    {
        return $this->has(MedicinePrescription::factory($count), 'medicinesData');
    }

    public function withMedicines($count = 1): PrescriptionFactory
    {
        return $this->has(Medicine::factory($count));
    }
}
