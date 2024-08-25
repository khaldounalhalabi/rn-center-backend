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
            SubscriptionSeeder::class,
            AvailableDepartmentSeeder::class,
            HospitalSeeder::class,
            UserSeeder::class,
        ]);

//        fake data
        $this->call([
            SpecialitySeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            AppointmentSeeder::class,
            MedicineSeeder::class,
            PrescriptionSeeder::class,
            EnquirySeeder::class,
            OfferSeeder::class,
            BloodDonationRequestSeeder::class,
            SystemOfferSeeder::class,
            SettingSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
