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
            'note' => fake()->text(),
            'service_id' => Service::factory(),
            'extra_fees' => fake()->randomFloat(1, 2000),
            'total_cost' => fake()->randomFloat(2, 0, 1000),
            'type' => fake()->randomElement(AppointmentTypeEnum::getAllValues()),
            'date' => fake()->dateTimeBetween('-5 days', '+20 days'),
            'from' => fake()->time(),
            'to' => fake()->time(),
            'status' => AppointmentStatusEnum::PENDING->value,
            'device_type' => fake()->word(),
            'appointment_sequence' => fake()->numberBetween(1, 10),
            //TODO::add qr code faker
        ];
    }

    public function withAppointmentLogs($count = 1)
    {
        return $this->has(\App\Models\AppointmentLog::factory($count));
    }

}
