<?php

namespace Database\Seeders;

use App\Models\AvailableDepartment;
use Illuminate\Database\Seeder;

class AvailableDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AvailableDepartment::factory(10)
            ->create();
    }
}
