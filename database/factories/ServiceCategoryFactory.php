<?php

namespace Database\Factories;

use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ServiceCategoryFactory extends Factory
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
            'name' => $this->fakeTranslation('word'),
        ];
    }
    public function withServices($count = 1)
    {
        return $this->has(\App\Models\Service::factory($count));
    }

}
