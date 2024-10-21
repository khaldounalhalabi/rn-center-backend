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
        User::factory()
            ->withPhoneNumbers()
            ->withAddress()->create(['email' => 'admin@pom.com', 'password' => 'BvgeGL9KDjAz3S'])
            ->assignRole(RolesPermissionEnum::ADMIN['role']);
    }
}
