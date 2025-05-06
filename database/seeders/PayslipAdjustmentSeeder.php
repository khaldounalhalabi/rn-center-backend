<?php

namespace Database\Seeders;

use App\Models\PayslipAdjustment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PayslipAdjustmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PayslipAdjustment::factory(10)->create();
    }
}
