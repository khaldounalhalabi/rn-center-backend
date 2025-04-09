<?php

namespace Database\Factories;

use App\Enums\WeekDayEnum;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\ClinicEmployee;
use App\Models\ClinicHoliday;
use App\Models\Medicine;
use App\Models\PatientProfile;
use App\Models\Prescription;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\User;
use App\Serializers\Translatable;
use App\Traits\FileHandler;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\File;

/**
 * @extends Factory
 */
class ClinicFactory extends Factory
{
    use FileHandler;

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => Translatable::fake('name'),
            'appointment_cost' => fake()->numberBetween(1, 100),
            'user_id' => User::factory(),
            'working_start_year' => fake()->date(),
            'max_appointments' => fake()->numberBetween(1, 10),
            'appointment_day_range' => 7,
            'about_us' => fake()->sentence,
            'experience' => fake()->sentence,
            'approximate_appointment_time' => random_int(1, 30),
        ];
    }

    public function allRelations(): ClinicFactory
    {
        return $this->withMedia()
            ->withSchedules()
            ->withSpecialities()
            ->withServices()
            ->withClinicHolidays()
            ->withPrescriptions();
    }

    public function withPrescriptions($count = 1): ClinicFactory
    {
        return $this->has(Prescription::factory($count));
    }

    public function withClinicHolidays($count = 1): ClinicFactory
    {
        return $this->has(ClinicHoliday::factory($count));
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
                    'schedulable_id' => $clinic->id,
                    'day_of_week' => $day,
                    'start_time' => Carbon::parse('09:00'),
                    'end_time' => Carbon::parse('21:00'),
                    'schedulable_type' => Clinic::class,
                ]);
            }
        });
    }

    public function withMedia(): ClinicFactory
    {
        return $this->afterCreating(function (Clinic $clinic) {
            $num = fake()->numberBetween(1, 4);
            $clinic->addMedia(
                new File(storage_path("/app/required/img$num.png"))
            )->preservingOriginal()->toMediaCollection();
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

    public function withPatientProfiles($count = 1): ClinicFactory
    {
        return $this->has(PatientProfile::factory($count));
    }

    public function withClinicEmployees($count = 1): ClinicFactory
    {
        return $this->has(ClinicEmployee::factory($count));
    }
}
