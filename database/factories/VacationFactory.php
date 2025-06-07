<?php

namespace Database\Factories;

use App\Enums\RolesPermissionEnum;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class VacationFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $userId = fake()->boolean()
            ? Clinic::inRandomOrder()->first()->user_id
            : User::byRole(RolesPermissionEnum::SECRETARY['role'])
                ->inRandomOrder()
                ->first()->id;

        return [
            'user_id' => $userId,
            'from' => now()->subDays(fake()->numberBetween(1, 3)),
            'to' => now()->addDays(fake()->numberBetween(1, 3)),
            'reason' => fake()->text(),
            'status' => fake()->word(),
            'cancellation_reason' => null,
        ];
    }
}
