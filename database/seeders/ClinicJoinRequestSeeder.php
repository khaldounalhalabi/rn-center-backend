<?php

namespace Database\Seeders;

use App\Models\ClinicJoinRequest;
use Illuminate\Database\Seeder;

class ClinicJoinRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClinicJoinRequest::factory(10)->create();
    }
}
