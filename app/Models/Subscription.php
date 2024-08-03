<?php

namespace App\Models;

use App\Enums\SubscriptionPeriodUnitEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string  name
 * @property string  description
 * @property numeric period
 * @property numeric allow_period
 * @property numeric cost
 */
class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'period',
        'period_unit',
        'allow_period',
        'cost',
    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'name',
        ];
    }

    public function exportable(): array
    {
        return [
            'name',
            'description',
            'period',
            'allow_period',
            'cost',
        ];
    }

    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class);
    }

    public function dayUnit(): bool
    {
        return $this->period_unit == SubscriptionPeriodUnitEnum::DAY->value;
    }

    public function monthUnit(): bool
    {
        return $this->period_unit == SubscriptionPeriodUnitEnum::MONTH->value;
    }
}
