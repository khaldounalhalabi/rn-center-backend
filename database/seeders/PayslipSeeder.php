<?php

namespace Database\Seeders;

use App\Models\Payslip;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PayslipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Payslip::factory(10)->create();
    }
}
