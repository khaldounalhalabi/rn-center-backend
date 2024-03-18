<?php

namespace Database\Factories;

use App\Enums\BloodGroupEnum;
use App\Enums\GenderEnum;
use App\Models\Address;
use App\Models\Clinic;
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
            'first_name' => $this->fakeTranslation('first_name'),
            'middle_name' => $this->fakeTranslation('last_name'),
            'last_name' => $this->fakeTranslation('last_name'),
            'email' => $this->faker->unique()->safeEmail(),
            'birth_date' => Carbon::now()->subYear(20),
            'gender' => $this->faker->randomElement(GenderEnum::getAllValues()),
            'blood_group' => $this->faker->randomElement(BloodGroupEnum::getAllValues()),
            'tags' => $this->faker->text(),
            'email_verified_at' => Carbon::now(),
            'password' => '123456789',
            'is_blocked' => false,
            'is_archived' => false,
        ];
    }

    public function withAddress()
    {
        return $this->has(Address::factory(), 'address');
    }

    public function allRelations(): UserFactory
    {
        return $this->withPhoneNumbers()
            ->withClinics()
            ->withMedia()
            ->withCustomer();
    }

    public function withCustomer(): UserFactory
    {
        return $this->has(Customer::factory());
    }

    public function withMedia(): UserFactory
    {
        return $this->afterCreating(function (User $user) {
            $user->addMedia(
                new File(storage_path('/app/required/download.png'))
            )->preservingOriginal()->toMediaCollection();
        });
    }

    public function withClinics(): UserFactory
    {
        return $this->has(Clinic::factory());
    }

    public function withPhoneNumbers($count = 1): UserFactory
    {
        return $this->has(PhoneNumber::factory($count), 'phones');
    }

}
