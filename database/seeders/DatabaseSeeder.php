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
        ]);

        $this->call([
            FormulaVariableSeeder::class,
            FormulaSeeder::class,
            UserSeeder::class,
            SpecialitySeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            ClinicSeeder::class,
            CustomerSeeder::class,
            HolidaySeeder::class,
            AppointmentSeeder::class,
            MedicineSeeder::class,
            PrescriptionSeeder::class,
            TransactionSeeder::class,
            PayrunSeeder::class,
            VacationSeeder::class,
            TaskSeeder::class,
            AssetSeeder::class,
            UserAssetSeeder::class,
        ]);
    }
}
