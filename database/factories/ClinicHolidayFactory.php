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
            'clinic_id' => Clinic::inRandomOrder()->first()->id,
            'start_date' => fake()->dateTimeBetween('-5 days', '+5 days'),
            'end_date' => fake()->dateTimeBetween('+10 days', '+20 days'),
            'reason' => $this->fakeTranslation('word'),
        ];
    }
}
