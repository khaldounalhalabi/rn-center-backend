<?php

namespace Database\Factories;

use App\Enums\AssetTypeEnum;
use App\Models\Asset;
use App\Models\UserAsset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(AssetTypeEnum::getAllValues());
        return [
            'name' => fake()->firstName(),
            'serial_number' => fake()->uuid(),
            'type' => $type,
            'quantity' => $type == AssetTypeEnum::ASSET->value ? 1 : fake()->numberBetween(1, 15),
            'purchase_date' => fake()->date(),
            'quantity_unit' => 'items',
        ];
    }

    public function configure(): AssetFactory
    {
        return $this->afterCreating(function (Asset $asset) {
            fakeImage($asset);
        });
    }

    public function withUserAssets($count = 1): AssetFactory
    {
        return $this->has(UserAsset::factory($count));
    }
}
