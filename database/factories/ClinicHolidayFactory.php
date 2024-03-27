<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ClinicHolidayFactory extends Factory
{
    use Translations;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'start_date' => fake()->unique()->date(),
            'end_date' => fake()->unique()->date(),
            'reason' => $this->fakeTranslation('word'),
        ];
    }
}
