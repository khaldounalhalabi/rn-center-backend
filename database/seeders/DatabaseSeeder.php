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
        ]);

        //fake data
        $this->call([
            UserSeeder::class,
        ]);
    }
}
