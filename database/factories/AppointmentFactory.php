<?php

namespace Database\Factories;

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\Clinic;
use App\Models\Customer;
use App\Models\Service;
use App\Models\SystemOffer;
use App\Traits\FileHandler;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class AppointmentFactory extends Factory
{
    use FileHandler;

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(AppointmentTypeEnum::getAllValues());
        return [
            'customer_id' => Customer::inRandomOrder()->first()?->id ?? Customer::factory()->create()?->id,
            'clinic_id' => Clinic::inRandomOrder()->first()?->id ?? Clinic::factory()->create()?->id,
            'note' => fake()->text(),
            'service_id' => Service::inRandomOrder()->first()?->id ?? Service::factory()->create()?->id,
            'extra_fees' => fake()->randomFloat(1, 2000),
            'total_cost' => fake()->randomFloat(2, 0, 1000),
            'type' => $type,
            'date' => fake()->dateTimeBetween('-5 days', '+20 days'),
            'status' => $type == AppointmentTypeEnum::MANUAL->value
                ? AppointmentStatusEnum::BOOKED->value
                : AppointmentStatusEnum::PENDING->value,
            'device_type' => fake()->word(),
            'appointment_sequence' => fake()->numberBetween(1, 10),
            'appointment_unique_code' => uniqid(),
        ];
    }

    public function allRelations(): AppointmentFactory
    {
        return $this->withAppointmentLogs();
    }

    public function withAppointmentLogs(): AppointmentFactory
    {
        return $this->afterCreating(function (Appointment $app) {
            AppointmentLog::create([
                'appointment_id' => $app->id,
                'status' => $app->status,
                'actor_id' => $app->clinic_id,
                'affected_id' => $app->customer_id,
                'happen_in' => now(),
            ]);
        });
    }

    public function withSystemOffers($count = 1): AppointmentFactory
    {
        return $this->has(SystemOffer::factory($count));
    }
}
