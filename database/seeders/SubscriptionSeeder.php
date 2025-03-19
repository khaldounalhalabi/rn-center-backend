<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Subscription::create([
            'name' => 'Online Appointments Deduction Based Subscription',
            'allow_period' => 0,
            'description' => "We agree (the service provider and you) on a deduction cost for each online appointment you get (the appointments that has been made from the service provider website or app)",
            'period' => -1,
            'cost' => 0
        ]);

        Subscription::create([
            'name' => '12 months subscription',
            'description' => 'you get 12 month subscription to use the application',
            'allow_period' => 7,
            'cost' => 300,
            'period' => 12
        ]);
    }
}
