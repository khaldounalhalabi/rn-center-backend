<?php

namespace Database\Factories;

use App\Enums\OfferTypeEnum;
use App\Models\Clinic;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class OfferFactory extends Factory
{
    use Translations;

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'     => $this->fakeTranslation('title'),
            'value'     => fake()->randomFloat(2, 0, 100),
            'note'      => $this->fakeTranslation('sentence'),
            'start_at'  => now()->subDays(5),
            'end_at'    => now()->addDays(5),
            'is_active' => true,
            'type'      => fake()->randomElement(OfferTypeEnum::getAllValues()),
            'clinic_id' => Clinic::factory(),
        ];
    }
}
