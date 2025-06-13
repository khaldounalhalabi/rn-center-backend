<?php

namespace Database\Seeders;

use App\Enums\RolesPermissionEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        $roles = RolesPermissionEnum::ALL;

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['role'], 'guard_name' => 'api']);
        }
    }
}
