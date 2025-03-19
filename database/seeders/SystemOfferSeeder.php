<?php

namespace Database\Seeders;

use App\Models\SystemOffer;
use Illuminate\Database\Seeder;

class SystemOfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemOffer::factory(10)->create();
    }
}
