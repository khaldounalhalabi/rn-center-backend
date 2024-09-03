<?php

namespace Database\Seeders;

use App\Enums\RolesPermissionEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Models\Clinic;
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

        ClinicSubscription::create([
            'start_time'      => now()->subDay(),
            'end_time'        => now()->addYear(),
            'clinic_id'       => $clinic->id,
            'status'          => SubscriptionStatusEnum::ACTIVE->value,
            'deduction_cost'  => 10,
            'subscription_id' => 2,
            'type'            => SubscriptionTypeEnum::MONTHLY_PAID_BASED->value,
        ]);


        $user2 = User::factory()
            ->withPhoneNumbers()
            ->withAddress()
            ->create([
                'email'    => 'asasimr55@gmail.com',
                'password' => '123456789',
            ])->assignRole(RolesPermissionEnum::DOCTOR['role']);

        $clinic = Clinic::factory()
            ->withSchedules()
            ->create([
            'name'    => new Translatable(['en' => 'pom', 'ar' => 'pom']),
            'user_id' => $user2->id,
            'appointment_cost' => 25000 ,
        ]);

        ClinicSubscription::create([
            'start_time'      => now()->subDay(),
            'end_time'        => now()->addYear(),
            'clinic_id'       => $clinic->id,
            'status'          => SubscriptionStatusEnum::ACTIVE->value,
            'deduction_cost'  => 10,
            'subscription_id' => 2,
            'type'            => SubscriptionTypeEnum::MONTHLY_PAID_BASED->value,
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
