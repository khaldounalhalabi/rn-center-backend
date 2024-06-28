<?php

namespace Database\Seeders;

use App\Models\AppointmentDeduction;
use Illuminate\Database\Seeder;

class AppointmentDeductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppointmentDeduction::factory(10)->create();
    }
}
