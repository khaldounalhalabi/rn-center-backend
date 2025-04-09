<?php

namespace Database\Factories;

use App\Enums\GenderEnum;
use App\Enums\RolesPermissionEnum;
use App\Models\Clinic;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\File;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => '123456789',
            'remember_token' => Str::random(10),
            'phone' => "09" . fake()->unique()->randomNumber(8, true),
            'gender' => fake()->randomElement(GenderEnum::getAllValues()),
        ];
    }

    public function verified(): Factory|UserFactory
    {
        return $this->state([
            'phone_verified_at' => now(),
        ]);
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
}
