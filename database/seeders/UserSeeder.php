<?php

namespace Database\Seeders;

use App\Enums\RolesPermissionEnum;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create(['email' => 'admin@pom.com', 'password' => '123456789'])->assignRole(RolesPermissionEnum::ADMIN['role']);
        User::factory()
            ->has(Clinic::factory()->allRelations())
            ->create([
                'email'    => 'khaldounalhalabi42@gmail.com',
                'password' => '123456789',
            ])
            ->assignRole(RolesPermissionEnum::DOCTOR['role']);
        User::factory()
            ->has(Clinic::factory()->allRelations())
            ->create([
                'email'    => 'asasimr55@gmail.com',
                'password' => '123456789',
            ])
            ->assignRole(RolesPermissionEnum::DOCTOR['role']);

        User::factory(10)->allRelations()->create();
    }
}
