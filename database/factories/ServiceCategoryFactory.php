<?php

namespace Database\Factories;

use App\Models\Service;
use App\Serializers\Translatable;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ServiceCategoryFactory extends Factory
{

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => Translatable::fake(),
        ];
    }

    public function allRelations(): ServiceCategoryFactory
    {
        return $this->withServices(5);
    }

    public function withServices($count = 1): ServiceCategoryFactory
    {
        return $this->has(Service::factory($count));
    }

}
