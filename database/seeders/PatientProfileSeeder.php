<?php

namespace Database\Seeders;

use App\Models\PatientProfile;
use Illuminate\Database\Seeder;

class PatientProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PatientProfile::factory(10)->create();
    }
}
