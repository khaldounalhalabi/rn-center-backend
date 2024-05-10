<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Customer;
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
        $physicalInformation = [
            "High Blood Pressure" => fake()->sentence(5),
            "Diabetic" => fake()->sentence(5),
            "Food Allergies" => fake()->sentence(5),
            "Tendency Bleed" => fake()->sentence(5),
            "Heart Disease" => fake()->sentence(5),
            "Medical History" => fake()->sentence(5),
            "Female Pregnancy" => fake()->sentence(5),
            "Breast Feeding" => fake()->sentence(5),
            "Current Medication" => fake()->sentence(5),
            "Surgery" => fake()->sentence(5),
            "Accident" => fake()->sentence(5),
            "Others" => fake()->sentence(5),
            "Pulse Rate" => fake()->sentence(5),
            "Temperature" => fake()->sentence(5),
        ];
        return [
            'clinic_id' => Clinic::factory(),
            'customer_id' => Customer::factory(),
            'appointment_id' => Appointment::factory(),
            'physical_information' => json_encode($physicalInformation),
            'problem_description' => fake()->text(),
            'test' => fake()->text(),
            'next_visit' => "Next Week",
        ];
    }

    public function allRelations(): PrescriptionFactory
    {
        return $this->withMedicineData();
    }

    public function withMedicines($count = 1): PrescriptionFactory
    {
        return $this->has(\App\Models\Medicine::factory($count));
    }

    public function withMedicineData($count = 1): PrescriptionFactory
    {
        return $this->has(MedicinePrescription::factory($count), 'medicinesData');
    }
}
