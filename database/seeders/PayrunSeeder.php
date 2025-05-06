<?php

namespace Database\Seeders;

use App\Models\Payrun;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PayrunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Payrun::factory(10)->create();
    }
}
