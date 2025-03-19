<?php

namespace Database\Seeders;

use App\Models\ClinicEmployee;
use Illuminate\Database\Seeder;

class ClinicEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClinicEmployee::factory(10)->create();
    }
}
