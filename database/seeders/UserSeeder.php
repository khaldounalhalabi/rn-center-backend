<?php

namespace Database\Seeders;

use App\Enums\RolesPermissionEnum;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->create(['email' => 'admin@pom.com', 'password' => '123456789'])->assignRole(RolesPermissionEnum::ADMIN['role']);

        User::factory(10)->allRelations()->create();
    }
}
