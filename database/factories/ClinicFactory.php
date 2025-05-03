<?php

namespace Database\Factories;

use App\Enums\RolesPermissionEnum;
use App\Enums\WeekDayEnum;
use App\Exceptions\RoleDoesNotExistException;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\User;
use App\Traits\FileHandler;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ClinicFactory extends Factory
{
    use FileHandler;

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     * @throws RoleDoesNotExistException
     */
    public function definition(): array
    {
        return [
            'appointment_cost' => fake()->randomNumber(2),
            'user_id' => User::factory()->create()->assignRole(RolesPermissionEnum::DOCTOR['role'])->id,
            'working_start_year' => fake()->date('Y'),
            'max_appointments' => fake()->numberBetween(1, 10),
        ];
    }

    public function allRelations(): ClinicFactory
    {
        return $this->withSchedules()
            ->withSpecialities()
            ->withServices()
            ->withPrescriptions();
    }

    public function withPrescriptions($count = 1): ClinicFactory
    {
        return $this->has(Prescription::factory($count));
    }

    public function withServices($count = 1): ClinicFactory
    {
        return $this->has(Service::factory($count));
    }

    public function withSpecialities($count = 1): ClinicFactory
    {
        return $this->has(Speciality::factory($count));
    }

    public function withSchedules(): ClinicFactory
    {
        return $this->afterCreating(function (Clinic $clinic) {
            foreach (WeekDayEnum::getAllValues() as $day) {
                Schedule::create([
                    'scheduleable_id' => $clinic->id,
                    'scheduleable_type' => Clinic::class,
                    'day_of_week' => $day,
                    'start_time' => Carbon::parse('09:00'),
                    'end_time' => Carbon::parse('21:00'),
                ]);
            }
        });
    }

    public function withAppointments($count = 1): ClinicFactory
    {
        return $this->has(Appointment::factory($count));
    }

    public function withMedicines($count = 1): ClinicFactory
    {
        return $this->has(Medicine::factory($count));
    }
}
