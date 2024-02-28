<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\User;
use App\Traits\FileHandler;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

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
            'name' => $this->fakeTranslation('word'),
            'appointment_cost' => fake()->randomFloat(2, 0, 1000),
            'user_id' => \App\Models\User::factory() ,
            'working_start_year' => fake()->date(),
            'max_appointments' => fake()->numberBetween(1, 2000),
            'appointment_day_range' => fake()->numberBetween(1, 2000),
            'about_us' => $this->fakeTranslation('word'),
            'experience' => $this->fakeTranslation('word'),
        ];
    }

    public function withMedia(): ClinicFactory
    {
        return $this->afterCreating(function (Clinic $clinic) {
            if (app()->environment('testing')) {
                $clinic->addMedia(UploadedFile::fake()->image('fake-image.png'));
            } else {
                $clinic->addMedia(fake()->image);
            }
        });
    }
}
