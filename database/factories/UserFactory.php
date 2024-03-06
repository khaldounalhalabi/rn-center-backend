<?php

namespace Database\Factories;

use App\Enums\BloodGroupEnum;
use App\Enums\GenderEnum;
use App\Models\Clinic;
use App\Models\Customer;
use App\Models\PhoneNumber;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

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
            'email_verified_at' => Carbon::now(),
            'password' => '123456789',
            'is_blocked' => false,
            'is_archived' => false
        ];
    }

    public function allRelations(): UserFactory
    {
        return $this->withMedia();
    }

    public function withMedia(): UserFactory
    {
        return $this->afterCreating(function (User $user) {
            if (app()->environment('testing')) {
                $user->addMedia(UploadedFile::fake()->image('fake-image.png'))->toMediaCollection();
            } else {
                $user->addMedia(fake()->image)->toMediaCollection();
            }
        });
    }

    public function withCustomer(): UserFactory
    {
        return $this->has(Customer::factory());
    }

    public function withClinics(): UserFactory
    {
        return $this->has(Clinic::factory());
    }

    public function withPhoneNumbers($count = 1)
    {
        return $this->has(PhoneNumber::factory($count));
    }

}
