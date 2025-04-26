<?php

namespace Database\Factories;

use App\Enums\MedicineStatusEnum;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class MedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'description' => fake()->text(),
            'barcode' => uniqid(),
            'status' => MedicineStatusEnum::EXISTS->value,
            'quantity' => fake()->numberBetween(10, 15),
        ];
    }

    public function withPrescriptions($count = 1): MedicineFactory
    {
        return $this->has(Prescription::factory($count));
    }
}
