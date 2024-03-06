<?php

namespace Database\Factories;

use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class AvailableDepartmentFactory extends Factory
{
    use \App\Traits\Translations;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->fakeTranslation('word'),
            'description' => $this->fakeTranslation('word'),
            'hospital_id' => Hospital::factory() ,

        ];
    }
}
