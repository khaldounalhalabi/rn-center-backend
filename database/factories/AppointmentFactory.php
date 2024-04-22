<?php

namespace Database\Factories;

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use App\Models\Clinic;
use App\Models\Customer;
use App\Models\Service;
use App\Traits\FileHandler;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class AppointmentFactory extends Factory
{
    use FileHandler;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'clinic_id' => Clinic::factory(),
            'note' => fake()->unique()->text(),
            'service_id' => Service::factory(),
            'extra_fees' => fake()->unique()->randomFloat(1, 2000),
            'total_cost' => fake()->unique()->randomFloat(2, 0, 1000),
            'type' => fake()->randomElement(AppointmentTypeEnum::getAllValues()),
            'date' => fake()->unique()->date(),
            'from' => fake()->unique()->time(),
            'to' => fake()->unique()->time(),
            'status' => fake()->randomElement(AppointmentStatusEnum::getAllValues()),
            'device_type' => fake()->unique()->word(),
            'appointment_sequence' => fake()->unique()->numberBetween(1, 2000),
            //TODO::add qr code faker
        ];
    }
}
