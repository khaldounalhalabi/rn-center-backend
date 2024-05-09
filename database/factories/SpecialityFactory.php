<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class SpecialityFactory extends Factory
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
            'name' => $this->fakeTranslation('name'),
            'description' => fake()->text(),
            'tags' => fake()->text(),
        ];
    }

    public function allRelations(): SpecialityFactory
    {
        return $this->withClinics();
    }

    public function withClinics($count = 1): SpecialityFactory
    {
        return $this->has(Clinic::factory($count));
    }
}
