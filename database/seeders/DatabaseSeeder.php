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
            SubscriptionSeeder::class,
            UserSeeder::class,
        ]);

        $this->call([
            SpecialitySeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            AppointmentSeeder::class,
            MedicineSeeder::class,
            PrescriptionSeeder::class,
        ]);
    }
}
