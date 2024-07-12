<?php

namespace Database\Seeders;

use App\Models\Follower;
use Illuminate\Database\Seeder;

class FollowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Follower::factory(10)->create();
    }
}
