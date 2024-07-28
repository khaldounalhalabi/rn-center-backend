<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use App\Enums\RolesPermissionEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Models\Appointment;
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
        $user = User::factory()
            ->has(Clinic::factory()->allRelations())
            ->create([
                'email' => 'khaldounalhalabi42@gmail.com',
                'password' => '123456789',
            ])
            ->assignRole(RolesPermissionEnum::DOCTOR['role']);
        Appointment::factory(10)->create([
            'clinic_id' => $user?->getClinicId(),
            'status' => AppointmentStatusEnum::PENDING,
            'type' => AppointmentTypeEnum::ONLINE->value,
        ]);
        ClinicSubscription::create([
            'start_time' => now()->subDay(),
            'end_time' => now()->addYear(),
            'clinic_id' => $user->getClinicId(),
            'status' => SubscriptionStatusEnum::ACTIVE->value,
            'deduction_cost' => 10,
            'subscription_id' => 2,
            'type' => SubscriptionTypeEnum::MONTHLY_PAID_BASED->value,
        ]);
        $user = User::factory()
            ->has(Clinic::factory()->allRelations())
            ->create([
                'email' => 'asasimr55@gmail.com',
                'password' => '123456789',
            ])
            ->assignRole(RolesPermissionEnum::DOCTOR['role']);

        Appointment::factory(10)->create([
            'clinic_id' => $user?->getClinicId(),
            'status' => AppointmentStatusEnum::PENDING,
            'type' => AppointmentTypeEnum::ONLINE->value,
        ]);

        ClinicSubscription::create([
            'start_time' => now()->subDay(),
            'end_time' => now()->addYear(),
            'clinic_id' => $user->getClinicId(),
            'status' => SubscriptionStatusEnum::ACTIVE->value,
            'deduction_cost' => 10,
            'subscription_id' => 2,
            'type' => SubscriptionTypeEnum::MONTHLY_PAID_BASED->value,
        ]);

        User::factory(10)->allRelations()->create();
    }
}
