<?php

namespace Database\Factories;

use App\Enums\SubscriptionStatusEnum;
use App\Models\Clinic;
use App\Models\ClinicSubscription;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClinicSubscriptionFactory extends Factory
{
    protected $model = ClinicSubscription::class;

    public function definition(): array
    {
        $sub = Subscription::inRandomOrder()->first();
        $endTime = fake()->dateTimeBetween("-4 days", '+1 years');
        return [
            'subscription_id' => $sub->id,
            'clinic_id'       => Clinic::factory(),
            'start_time'      => fake()->dateTimeBetween("-1 years", 'today'),
            'end_time'        => $endTime,
            'status'          => Carbon::parse($endTime)->isBefore(now()) ? SubscriptionStatusEnum::IN_ACTIVE->value : SubscriptionStatusEnum::ACTIVE->value,
            'deduction_cost'  => fake()->numberBetween(5, 15),
        ];
    }
}
