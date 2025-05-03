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
        $admin = User::factory()->verified()
            ->create([
                'phone' => '0936955531',
                'first_name' => 'Admin',
            ])->assignRole(RolesPermissionEnum::ADMIN['role']);

        $doctor = User::factory()->verified()
            ->create([
                'phone' => '0936955532',
                'first_name' => 'Doctor',
            ])->assignRole(RolesPermissionEnum::DOCTOR['role']);

        Clinic::factory()
            ->allRelations()
            ->create([
                'user_id' => $doctor->id,
            ]);

        $secretary = User::factory()->verified()
            ->withSchedules()
            ->create([
                'phone' => '0936955533',
                'first_name' => 'Secretary',
            ])->assignRole(RolesPermissionEnum::SECRETARY['role']);



        $patient = User::factory()->verified()
            ->customer()
            ->create([
                'phone' => '0936955534',
                'first_name' => 'Patient',
            ])->assignRole(RolesPermissionEnum::CUSTOMER['role']);
    }
}
