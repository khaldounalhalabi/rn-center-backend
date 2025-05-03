<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\File;

/**
 * @extends Factory
 */
class ServiceFactory extends Factory
{

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'approximate_duration' => fake()->numberBetween(1, 2000),
            'service_category_id' => ServiceCategory::inRandomOrder()->first()?->id ?? ServiceCategory::factory()->create()->id,
            'price' => fake()->randomNumber(2),
            'description' => fake()->name,
            'clinic_id' => Clinic::inRandomOrder()->first()?->id ?? Clinic::factory()->create()->id,
        ];
    }

    public function withAppointments($count = 1): ServiceFactory
    {
        return $this->has(Appointment::factory($count));
    }


    public function withMedia(): ServiceFactory
    {
        return $this->afterCreating(function (Service $service) {
            $num = fake()->numberBetween(1, 4);
            $service->addMedia(
                new File(storage_path("/app/required/img$num.png"))
            )->preservingOriginal()->toMediaCollection();
        });
    }
}
