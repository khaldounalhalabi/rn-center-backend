<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Schedule;
use App\Models\Speciality;
use App\Models\User;
use App\Traits\FileHandler;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;

/**
 * @extends Factory
 */
class ClinicFactory extends Factory
{
    use FileHandler;
    use Translations;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->fakeTranslation('name'),
            'appointment_cost' => fake()->numberBetween(1, 100),
            'user_id' => User::factory()->withPhoneNumbers()->withAddress(),
            'working_start_year' => fake()->date(),
            'max_appointments' => fake()->numberBetween(1, 2000),
            'appointment_day_range' => fake()->numberBetween(1, 2000),
            'about_us' => fake()->sentence,
            'experience' => fake()->sentence,
        ];
    }

    public function allRelations(): ClinicFactory
    {
        return $this->withMedia()
            ->withSchedules(5)
            ->withSpecialities(5);
    }

    public function withSpecialities($count = 1): ClinicFactory
    {
        return $this->has(Speciality::factory($count));
    }

    public function withSchedules($count = 1): ClinicFactory
    {
        return $this->has(Schedule::factory($count) , 'schedules');
    }

    public function withMedia(): ClinicFactory
    {
        return $this->afterCreating(function (Clinic $clinic) {
            $clinic->addMedia(
                new File(storage_path('/app/required/download.png'))
            )->preservingOriginal()->toMediaCollection();
        });
    }
}
