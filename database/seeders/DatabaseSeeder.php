<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        $this->call([
            SpecialitySeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            ClinicSeeder::class,
            ScheduleSeeder::class,
            AppointmentSeeder::class,
            MedicineSeeder::class,
            PrescriptionSeeder::class,
            HolidaySeeder::class,
        ]);
    }
}
