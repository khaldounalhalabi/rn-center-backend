<?php

namespace Database\Seeders;

use App\Enums\RolesPermissionEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Models\Clinic;
use App\Models\ClinicEmployee;
use App\Models\ClinicSubscription;
use App\Models\User;
use App\Serializers\Translatable;
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
            ->withAddress()->create(['email' => 'admin@pom.com', 'password' => '123456789'])
            ->assignRole(RolesPermissionEnum::ADMIN['role']);

        $user1 = User::factory()
            ->withPhoneNumbers()
            ->withAddress()->create([
                'email'    => 'khaldounalhalabi42@gmail.com',
                'password' => '123456789',
            ])->assignRole(RolesPermissionEnum::DOCTOR['role']);

        $clinic = Clinic::factory()->create([
            'name'    => new Translatable(['en' => 'Almahaba', 'ar' => 'عيادة المحبة']),
            'user_id' => $user1->id,
        ]);

        ClinicEmployee::factory()
            ->create([
                'user_id'   => User::factory()
                    ->create([
                        'email'       => 'khaldoun1222@hotmail.com',
                        'password'    => '123456789',
                        'first_name'  => 'staff',
                        'middle_name' => 'staff',
                        'last_name'   => 'staff',
                        'is_blocked'  => false,
                        'is_archived' => false,
                    ])->assignRole(RolesPermissionEnum::CLINIC_EMPLOYEE['role'])->id,
                'clinic_id' => $clinic->id
            ]);

        ClinicSubscription::create([
            'start_time'      => now()->subDay(),
            'end_time'        => now()->addYear(),
            'clinic_id'       => $clinic->id,
            'status'          => SubscriptionStatusEnum::ACTIVE->value,
            'deduction_cost'  => 10,
            'subscription_id' => 2,
            'type'            => SubscriptionTypeEnum::BOOKING_COST_BASED->value,
        ]);

        $user1 = User::factory()
            ->withPhoneNumbers()
            ->withAddress()->create([
                'email'    => 'asasimr55@gmail.com',
                'password' => '123456789',
            ])->assignRole(RolesPermissionEnum::DOCTOR['role']);

        $clinic = Clinic::factory()->create([
            'name'    => new Translatable(['en' => 'POM', 'ar' => 'POM']),
            'user_id' => $user1->id,
        ]);

        ClinicEmployee::factory()
            ->create([
                'user_id'   => User::factory()
                    ->create([
                        'email'       => 'asasimr55@staff.com',
                        'password'    => '123456789',
                        'first_name'  => 'staff',
                        'middle_name' => 'staff',
                        'last_name'   => 'staff',
                        'is_blocked'  => false,
                        'is_archived' => false,
                    ])->assignRole(RolesPermissionEnum::CLINIC_EMPLOYEE['role'])->id,
                'clinic_id' => $clinic->id
            ]);

        ClinicSubscription::create([
            'start_time'      => now()->subDay(),
            'end_time'        => now()->addYear(),
            'clinic_id'       => $clinic->id,
            'status'          => SubscriptionStatusEnum::ACTIVE->value,
            'deduction_cost'  => 10,
            'subscription_id' => 2,
            'type'            => SubscriptionTypeEnum::BOOKING_COST_BASED->value,
        ]);

        User::factory(2)
            ->withPhoneNumbers()
            ->withAddress()
            ->clinic()
            ->create();

        User::factory(2)
            ->withPhoneNumbers()
            ->withAddress()
            ->customer()
            ->create();
    }
}
