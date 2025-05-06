<?php

namespace Database\Factories;

use App\Enums\PayslipStatusEnum;
use App\Models\Formula;
use App\Models\Payrun;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class PayslipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payrun_id' => Payrun::factory(),
            'user_id' => User::factory()->secretary(),
            'formula_id' => Formula::factory(),
            'paid_days' => fake()->unique()->numberBetween(1, 2000),
            'gross_pay' => fake()->unique()->randomFloat(1, 2000),
            'net_pay' => fake()->unique()->randomFloat(1, 2000),
            'status' => fake()->randomElement(PayslipStatusEnum::getAllValues()),
            'edited_manually' => false,
        ];
    }

    public function withPayslipAdjustments($count = 1)
    {
        return $this->has(\App\Models\PayslipAdjustment::factory($count));
    }
}
