<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\AvailableDepartment;
use App\Models\Hospital;
use App\Models\PhoneNumber;
use App\Traits\FileHandler;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\File;

/**
 * @extends Factory
 */
class HospitalFactory extends Factory
{
    use FileHandler;
    use Translations;

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->fakeTranslation('word'),
        ];
    }

    public function withMedia(): HospitalFactory
    {
        return $this->afterCreating(function (Hospital $h) {
            $h->addMedia(
                new File(storage_path('/app/required/download.png'))
            )->preservingOriginal()->toMediaCollection();
        });
    }

    public function withPhoneNumbers($count = 1): HospitalFactory
    {
        return $this->has(PhoneNumber::factory($count), 'phones');
    }

    public function withAvailableDepartments($count = 1): HospitalFactory
    {
        return $this->has(AvailableDepartment::factory($count), 'availableDepartments');
    }

    public function withAddress(): HospitalFactory
    {
        return $this->afterCreating(function (Hospital $hos) {
            Address::factory()->create([
                'addressable_type' => Hospital::class,
                'addressable_id'   => $hos->id
            ]);
        });
    }

    public function allRelations(): HospitalFactory
    {
        return $this->withMedia()
            ->withAvailableDepartments()
            ->withPhoneNumbers()
            ->withAddress();
    }
}
