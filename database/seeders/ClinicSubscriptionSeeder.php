<?php

namespace Database\Seeders;

use App\Models\ClinicSubscription;
use Illuminate\Database\Seeder;

class ClinicSubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClinicSubscription::factory(40)->create();
    }
}
