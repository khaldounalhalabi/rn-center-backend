<?php

namespace Database\Seeders;

use App\Enums\AssetTypeEnum;
use App\Models\Asset;
use App\Models\User;
use App\Services\AssetService;
use Illuminate\Database\Seeder;

class UserAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::role(collect(["doctor", "secretary"]))
            ->get();

        $assets = Asset::all();

        for ($i = 0; $i < 5; $i++) {
            $asset = $assets->random();
            AssetService::make()->checkin([
                'asset_id' => $asset->id,
                'user_id' => $users->random()->id,
                'quantity' => $asset->type == AssetTypeEnum::CONSUMABLE->value
                    ? fake()->numberBetween(1, $asset->quantity)
                    : 1,
                'checkin_condition' => fake()->numberBetween(1, 10),
                'expected_return_date' => $asset->type == AssetTypeEnum::CONSUMABLE->value
                    ? null
                    : now()->addDays(fake()->numberBetween(100, 200))->format('Y-m-d'),
            ]);
        }
    }
}
