<?php

namespace Database\Factories;

use App\Enums\RolesPermissionEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type'        => fake()->randomElement(TransactionTypeEnum::getAllValues()),
            'amount'      => fake()->randomFloat(1, 2000),
            'description' => fake()->text(),
            'date'        => fake()->dateTimeBetween('-5 days', '+30 days'),
            'actor_id'    => User::inRandomOrder()->byRole(RolesPermissionEnum::ADMIN['role'])->first()->id,
        ];
    }
}
