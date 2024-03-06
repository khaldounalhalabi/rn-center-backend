<?php

namespace Database\Factories;

use App\Models\AvailableDepartment;
use App\Models\Hospital;
use App\Models\PhoneNumber;
use App\Traits\FileHandler;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

/**
 * @extends Factory
 */
class HospitalFactory extends Factory
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
        ];
    }

    public function withMedia(): HospitalFactory
    {
        return $this->afterCreating(function (Hospital $h) {
            $h->addMedia(
                app()->environment('testing')
                    ? UploadedFile::fake()->image('test.png')
                    : fake()->image
            )->toMediaCollection();
        });
    }

    public function withPhoneNumbers($count = 1): HospitalFactory
    {
        return $this->has(PhoneNumber::factory($count));
    }

    public function withAvailableDepartments($count = 1): HospitalFactory
    {
        return $this->has(AvailableDepartment::factory($count));
    }

}
