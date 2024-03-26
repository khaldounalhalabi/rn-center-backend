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
        // needed data
        $this->call([
            RoleSeeder::class,
            CitySeeder::class,
        ]);

        //fake data
        $this->call([
            UserSeeder::class,
            ClinicSeeder::class,
            AddressSeeder::class,
            AvailableDepartmentSeeder::class,
            CustomerSeeder::class,
            HospitalSeeder::class,
            PhoneNumberSeeder::class,
            ScheduleSeeder::class,
            SpecialitySeeder::class
        ]);
    }
}
