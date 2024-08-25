<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class FollowerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clinic_id'   => Clinic::factory(),
            'customer_id' => Customer::factory(),
        ];
    }
}
