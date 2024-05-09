<?php

namespace Database\Seeders;

use App\Models\ClinicHoliday;
use Illuminate\Database\Seeder;

class ClinicHolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClinicHoliday::factory(10)
            ->create();
    }
}
