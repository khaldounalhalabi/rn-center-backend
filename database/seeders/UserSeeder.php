<?php

namespace Database\Seeders;

use App\Enums\RolesPermissionEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Models\Clinic;
use App\Models\ClinicSubscription;
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
        $clinic = User::factory()
            ->has(Clinic::factory()->allRelations())
            ->create([
                'email'    => 'khaldounalhalabi42@gmail.com',
                'password' => '123456789',
            ])
            ->assignRole(RolesPermissionEnum::DOCTOR['role']);
        ClinicSubscription::create([
            'start_time'      => now()->subDay(),
            'end_time'        => now()->addYear(),
            'clinic_id'       => $clinic->id,
            'status'          => SubscriptionStatusEnum::ACTIVE->value,
            'deduction_cost'  => 10,
            'subscription_id' => 1,
            'type'            => SubscriptionTypeEnum::MONTHLY_PAID_BASED->value,
        ]);
        $clinic = User::factory()
            ->has(Clinic::factory()->allRelations())
            ->create([
                'email'    => 'asasimr55@gmail.com',
                'password' => '123456789',
            ])
            ->assignRole(RolesPermissionEnum::DOCTOR['role']);

        ClinicSubscription::create([
            'start_time'      => now()->subDay(),
            'end_time'        => now()->addYear(),
            'clinic_id'       => $clinic->id,
            'status'          => SubscriptionStatusEnum::ACTIVE->value,
            'deduction_cost'  => 10,
            'subscription_id' => 1,
            'type'            => SubscriptionTypeEnum::MONTHLY_PAID_BASED->value,
        ]);

        User::factory(10)->allRelations()->create();
    }
}
