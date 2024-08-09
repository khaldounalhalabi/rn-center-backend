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
        'deduction_cost',
        'type',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function remainingTime(): ?string
    {
        return str_replace(['before', 'after'], '', $this->end_time->diffForHumans(now()));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('end_time', '>', now()->format('Y-m-d'))
            ->where('status', SubscriptionStatusEnum::ACTIVE->value);
    }

    public function scopeInActive(Builder $query): Builder
    {
        return $query->where('end_time', '<=', now()->format('Y-m-d'));
    }
}
