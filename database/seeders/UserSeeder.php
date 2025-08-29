<?php

namespace Database\Seeders;

use App\Enums\RolesPermissionEnum;
use App\Models\Clinic;
use App\Models\Customer;
use App\Models\Speciality;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialities = Speciality::all();
        $admin = User::factory()->verified()
            ->create([
                'phone' => '0936955531',
            ])->assignRole(RolesPermissionEnum::ADMIN['role']);

        $doctor = User::factory()->verified()
            ->create([
                'phone' => '0936955532',
            ])->assignRole(RolesPermissionEnum::DOCTOR['role']);

        $clinic = Clinic::factory()
            ->withSchedules()
            ->create([
                'user_id' => $doctor->id,
            ]);

        $clinic->specialities()
            ->attach($specialities->random(3));

        $secretary = User::factory()
            ->verified()
            ->secretary()
            ->create([
                'phone' => '0936955533',
            ]);


        $patient = User::factory()->verified()
            ->customer()
            ->create([
                'phone' => '0936955534',
            ])->assignRole(RolesPermissionEnum::CUSTOMER['role']);

        User::factory(5)
            ->secretary()
            ->create();

        $clinics = User::factory(5)->clinic()->create()->load(['clinic'])->map->clinic;
        foreach ($clinics as $clinic) {
            $clinic->specialities()
                ->attach($specialities->random(3));
        }

        Customer::factory(5)->create();
    }
}
