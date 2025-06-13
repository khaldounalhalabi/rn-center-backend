<?php

namespace Database\Factories;

use App\Enums\RolesPermissionEnum;
use App\Enums\VacationStatusEnum;
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
            : User::role(RolesPermissionEnum::SECRETARY['role'])
                ->inRandomOrder()
                ->first()->id;

        return [
            'user_id' => $userId,
            'from' => now()->subDays(fake()->numberBetween(1, 3)),
            'to' => now()->addDays(fake()->numberBetween(1, 3)),
            'reason' => fake()->text(),
            'status' => VacationStatusEnum::DRAFT->value,
            'cancellation_reason' => null,
        ];
    }
}
