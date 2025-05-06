<?php

namespace Database\Factories;

use App\Enums\PayslipAdjustmentTypeEnum;
use App\Models\Payslip;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class PayslipAdjustmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payslip_id' => Payslip::factory(),
            'amount'      => fake()->unique()->randomFloat(1, 2000),
            'reason'      => fake()->unique()->text(),
            'type'        => fake()->randomElement(PayslipAdjustmentTypeEnum::getAllValues()),
        ];
    }
}
