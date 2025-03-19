<?php

namespace Database\Seeders;

use App\Models\ClinicTransaction;
use Illuminate\Database\Seeder;

class ClinicTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClinicTransaction::factory(10)->create();
    }
}
