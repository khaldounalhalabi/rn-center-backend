<?php

namespace Database\Factories;

use App\Models\Medicine;
use App\Models\MedicinePrescription;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

class MedicinePrescriptionFactory extends Factory
{
    protected $model = MedicinePrescription::class;

    public function definition(): array
    {
        return [
            'prescription_id' => Prescription::factory(),
            'medicine_id' => Medicine::factory(),
            'dosage' => $this->faker->word(),
            'dose_interval' => $this->faker->word(),
            'comment' => $this->faker->word(),
        ];
    }
}
