<?php

namespace Database\Seeders;

use App\Models\UserAsset;
use Illuminate\Database\Seeder;

class UserAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserAsset::factory(10)->create();
    }
}
