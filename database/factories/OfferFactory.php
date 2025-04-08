<?php

namespace Database\Factories;

use App\Enums\OfferTypeEnum;
use App\Models\Clinic;
use App\Serializers\Translatable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class OfferFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => Translatable::fake('word'),
            'value' => fake()->randomFloat(2, 0, 100),
            'note' => Translatable::fake('sentence'),
            'start_at' => now()->subDays(5),
            'end_at' => now()->addDays(5),
            'is_active' => true,
            'type' => fake()->randomElement(OfferTypeEnum::getAllValues()),
            'clinic_id' => Clinic::inRandomOrder()->first()->id,
        ];
    }
}
