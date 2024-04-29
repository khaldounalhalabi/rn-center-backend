<?php

namespace Database\Seeders;

use App\Models\AppointmentLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppointmentLog::factory(10)->create() ;
    }
}
