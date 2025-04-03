<?php

namespace Database\Factories;

use App\Enums\BloodGroupEnum;
use App\Enums\GenderEnum;
use App\Enums\RolesPermissionEnum;
use App\Models\Address;
use App\Models\Clinic;
use App\Models\ClinicEmployee;
use App\Models\Customer;
use App\Models\PhoneNumber;
use App\Models\User;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\File;
use Illuminate\Support\Carbon;

class UserFactory extends Factory
{
    use Translations;

    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->fakeTranslation('firstName'),
            'last_name' => $this->fakeTranslation('lastName'),
            'email' => $this->faker->unique()->safeEmail(),
            'birth_date' => Carbon::now()->subYear(20),
            'gender' => $this->faker->randomElement(GenderEnum::getAllValues()),
            'blood_group' => $this->faker->randomElement(BloodGroupEnum::getAllValues()),
            'email_verified_at' => Carbon::now(),
            'password' => '123456789',
        ];
    }

    public function allRelations(): UserFactory
    {
        return $this->withPhoneNumbers(1)
            ->withAddress()
            ->withMedia();
    }

    public function withMedia(): UserFactory
    {
        return $this->afterCreating(function (User $user) {
            $user->addMedia(
                new File(storage_path('/app/required/download.png'))
            )->preservingOriginal()->toMediaCollection();
        });
    }

    public function withAddress(): UserFactory
    {
        return $this->afterCreating(function (User $user) {
            Address::factory()->create([
                'addressable_id' => $user->id,
                'addressable_type' => User::class,
            ]);
        });
    }

    public function withPhoneNumbers($count = 1): UserFactory
    {
        return $this->afterCreating(function (User $user) use ($count) {
            PhoneNumber::factory($count)->create([
                'phoneable_type' => User::class,
                'phoneable_id' => $user->id,
            ]);
        });
    }

    public function customer(): UserFactory
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(RolesPermissionEnum::CUSTOMER['role']);
            Customer::factory()->create([
                'user_id' => $user->id,
            ]);
        });
    }

    public function clinic(): UserFactory
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(RolesPermissionEnum::DOCTOR['role']);
            Clinic::factory()->withSchedules()->create([
                'user_id' => $user->id,
            ]);
        });
    }

    public function admin(): UserFactory
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(RolesPermissionEnum::ADMIN['role']);
        });
    }

    public function withClinicEmployees($count = 1): UserFactory
    {
        return $this->has(ClinicEmployee::factory($count));
    }
}
