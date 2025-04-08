<?php

namespace App\Models;

use App\Enums\SubscriptionStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'clinic_id',
        'start_time',
        'end_time',
        'status',
        'type',
        'is_paid',
        'end_time_with_allow_period'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_paid' => 'boolean',
        'end_time_with_allow_period' => 'datetime'
    ];

    protected static function booted()
    {
        self::creating(function (ClinicSubscription $clinicSubscription) {
            $subscription = Subscription::find($clinicSubscription->subscription_id);
            $clinicSubscription->end_time_with_allow_period = $clinicSubscription->end_time->addDays($subscription->allow_period);
        });

        self::updating(function (ClinicSubscription $clinicSubscription) {
            $subscription = Subscription::find($clinicSubscription->subscription_id);
            $clinicSubscription->end_time_with_allow_period = $clinicSubscription->end_time->addDays($subscription->allow_period);
        });
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function remainingTime(): int
    {
        return $this->end_time->diffInDays(now());
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('end_time_with_allow_period', '>', now()->format('Y-m-d H:i:s'))
            ->where('start_time', '<=', now()->format('Y-m-d H:i:s'))
            ->where('status', SubscriptionStatusEnum::ACTIVE->value);
    }

    public function scopeInActive(Builder $query): Builder
    {
        return $query->where('end_time_with_allow_period', '<=', now()->format('Y-m-d'));
    }
}
