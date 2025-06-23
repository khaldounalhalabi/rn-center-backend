<?php

namespace Database\Factories;

use App\Enums\AssetStatusEnum;
use App\Enums\RolesPermissionEnum;
use App\Models\Asset;
use App\Models\User;
use App\Models\UserAsset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class UserAssetFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $asset = Asset::factory()->create();
        return [
            'asset_id' => $asset->id,
            'user_id' => User::role(
                fake()->boolean()
                    ? RolesPermissionEnum::DOCTOR['role']
                    : RolesPermissionEnum::SECRETARY['role']
            )->inRandomOrder()->first()->id,

            'status' => fake()->randomElement(AssetStatusEnum::getAllValues()),
            'checkin_condition' => fake()->numberBetween(1, 10),
            'checkout_condition' => fake()->numberBetween(1, 10),
            'checkin_date' => fake()->date(),
            'checkout_date' => fake()->date(),
            'quantity' => fake()->numberBetween(1, $asset->quantity)
        ];
    }

    public function configure(): UserAssetFactory
    {
        return $this->afterCreating(function (UserAsset $userAsset) {
            $userAsset->update([
                'quantity' => fake()->numberBetween(1, $userAsset->asset->quantity),
            ]);
        });
    }
}
