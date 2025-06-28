<?php

namespace Database\Seeders;

use App\Models\PatientStudy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatientStudySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PatientStudy::factory(10)->create();
    }
}
