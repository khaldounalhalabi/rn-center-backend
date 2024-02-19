<?php

namespace Database\Factories;

use App\Enums\BloodGroupEnum;
use App\Enums\GenderEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->name(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->phoneNumber(),
            'birth_date' => Carbon::now()->subYear(20),
            'gender' => $this->faker->randomElement(GenderEnum::getAllValues()),
            'blood_group' => $this->faker->randomElement(BloodGroupEnum::getAllValues()),
            'tags' => $this->faker->text(),
            'image' => $this->faker->word(),
            'email_verified_at' => Carbon::now(),
            'password' => '123456789',
        ];
    }
}
