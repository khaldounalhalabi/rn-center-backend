<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Speciality;
use App\Serializers\Translatable;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\File;

/**
 * @extends Factory
 */
class SpecialityFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => Translatable::fake(),
            'description' => fake()->text(),
        ];
    }

    public function allRelations(): SpecialityFactory
    {
        return $this->withClinics()->withMedia();
    }

    public function withMedia(): SpecialityFactory
    {
        return $this->afterCreating(function (Speciality $spec) {
            $num = fake()->numberBetween(1, 4);
            $spec->addMedia(
                new File(storage_path("/app/required/img$num.png"))
            )->preservingOriginal()->toMediaCollection();
        });
    }

    public function withClinics($count = 1): SpecialityFactory
    {
        return $this->has(Clinic::factory($count));
    }
}
