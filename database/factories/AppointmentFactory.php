<?php

namespace Database\Factories;

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\Clinic;
use App\Models\Customer;
use App\Models\Service;
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
        $clinic = Clinic::factory()->withSchedules()->create();
        $service = Service::factory()->create([
            'clinic_id' => $clinic->id,
        ]);
        return [
            'customer_id' => Customer::factory(),
            'clinic_id' => $clinic->id,
            'note' => fake()->text(),
            'service_id' => $service->id,
            'extra_fees' => 0,
            'total_cost' => $service->price + $clinic->appointment_cost,
            'status' => AppointmentStatusEnum::BOOKED->value,
            'type' => fake()->randomElement(AppointmentTypeEnum::getAllValues()),
            'date_time' => fake()->dateTimeBetween('-3 days', '+3 days')->format('Y-m-d') . ' ' . $clinic->schedules()->first()?->start_time?->format('H:i'),
            'appointment_sequence' => 1,
            'discount' => 0
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
}
