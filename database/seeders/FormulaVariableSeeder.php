<?php

namespace Database\Seeders;

use App\Models\FormulaVariable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FormulaVariableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FormulaVariable::factory(10)->create();
    }
}
