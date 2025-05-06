<?php

namespace Database\Factories;

use App\Enums\PayrunStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory
 */
class PayrunFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = Carbon::parse(fake()->dateTimeBetween('-2 months', '+2 months'));
        $periodDate = Carbon::parse(fake()->dateTimeBetween('-2 months', '+2 months'));
        $period = $periodDate->copy()->startOfMonth()->format('Y-m-d') . ' - ' . $periodDate->copy()->endOfMonth()->format('Y-m-d');

        return [
            'status'              => PayrunStatusEnum::DRAFT->value,
            'should_delivered_at' => $date,
            'payment_date'        => $date->monthName . " , " . $date->year,
            'period'              => $period,
            'payment_cost'        => 0,
            'from'                => $periodDate->copy()->subMonth(),
            'to'                  => $periodDate->copy()->endOfMonth(),
            'has_errors' => false,
            'processed_at' => now(),
        ];
    }

    public function withPayslips($count = 1)
    {
        return $this->has(\App\Models\Payslip::factory($count));
    }
}
