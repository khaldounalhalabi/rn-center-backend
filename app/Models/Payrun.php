<?php

namespace App\Models;

use App\Enums\PayrunStatusEnum;
use App\Enums\PayslipStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string      status
 * @property Carbon      should_delivered_at
 * @property string      payment_date
 * @property numeric     payment_cost
 * @property string      period
 * @property Carbon      from
 * @property Carbon      to
 * @property Carbon|null processed_at
 * @property bool        has_errors
 */
class Payrun extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'should_delivered_at',
        'payment_date',
        'payment_cost',
        'period',
        'from',
        'to',
        'has_errors',
        'processed_at',

    ];

    protected $casts = [
        'should_delivered_at' => 'datetime',
        'from' => 'datetime',
        'to' => 'datetime',
        'has_errors' => 'boolean',
        'processed_at' => 'datetime',
    ];

    public function exportable(): array
    {
        return [
            'status',
            'should_delivered_at',
            'payment_date',
            'payment_cost',
            'period',
            'from',
            'to',
            'has_errors',
            'processed_at',
        ];
    }

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'status',
            'should_delivered_at',
            'payment_date',
            'payment_cost',
            'period',
            'from',
            'to',
        ];
    }

    public function scopeHasErrors($query)
    {
        return $query->where('has_errors', 1);
    }

    public function canUpdate(): bool
    {
        return $this->status != PayrunStatusEnum::DONE->value;
    }

    public function canDelete(): bool
    {
        return $this->status == PayrunStatusEnum::DRAFT->value;
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'payslips', 'payrun_id', 'user_id');
    }

    public function processedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'payslips', 'payrun_id', 'user_id')
            ->whereHas('payslips', function (Builder $builder) {
                $builder->where('payslips.status', '!=', PayslipStatusEnum::EXCLUDED->value);
            });
    }

    public function excludedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'payslips', 'payrun_id', 'user_id')
            ->whereHas('payslips', function (Builder $builder) {
                $builder->where('status', PayslipStatusEnum::EXCLUDED->value);
            });
    }

    public function calculatePaymentCost(): static
    {
        $cost = $this->payslips()
            ->where('status', '!=', PayslipStatusEnum::EXCLUDED->value)
            ->sum('net_pay');

        $this->update([
            'payment_cost' => $cost,
        ]);
        return $this;
    }

    public function getPaymentCost(): float|int
    {
        return $this->payslips()
            ->where('status', '!=', PayslipStatusEnum::EXCLUDED->value)
            ->sum('net_pay');
    }

    public function erroredPayslips(): HasMany
    {
        return $this->hasMany(Payslip::class)->whereNotNull('error');
    }
}
