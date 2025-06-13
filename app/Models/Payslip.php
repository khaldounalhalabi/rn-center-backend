<?php

namespace App\Models;

use App\Enums\ExcelColumnsTypeEnum;
use App\Enums\PayrunStatusEnum;
use App\Enums\PayslipAdjustmentTypeEnum;
use App\Enums\PayslipStatusEnum;
use App\Enums\PermissionEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int        payrun_id
 * @property int        user_id
 * @property int        formula_id
 * @property numeric    paid_days
 * @property numeric    gross_pay
 * @property numeric    net_pay
 * @property string     status
 * @property null|array error
 * @property null|array details
 * @property Payrun     payrun
 * @property User       user
 * @property Formula    formula
 * @property boolean    edited_manually
 */
class Payslip extends Model
{
    use HasFactory;

    protected $fillable = [
        'payrun_id',
        'user_id',
        'formula_id',
        'paid_days',
        'gross_pay',
        'net_pay',
        'status',
        'error',
        'details',
        'edited_manually',
    ];

    protected $casts = [
        'gross_pay' => 'double',
        'net_pay' => 'double',
        'error' => 'array',
        'details' => 'array',
        'edited_manually' => 'boolean',
    ];

    public function exportable(): array
    {
        return [
            'user.full_name' => ExcelColumnsTypeEnum::STRING,
            'formula.name' => ExcelColumnsTypeEnum::STRING,
            'paid_days' => ExcelColumnsTypeEnum::NUMERIC,
            'gross_pay' => ExcelColumnsTypeEnum::NUMERIC,
            'net_pay' => ExcelColumnsTypeEnum::NUMERIC,
            'benefits_sum_amount' => ExcelColumnsTypeEnum::NUMERIC,
            'deductions_sum_amount' => ExcelColumnsTypeEnum::NUMERIC,
            'status' => PayslipStatusEnum::getAllValues(),
        ];
    }

    public function payrun(): BelongsTo
    {
        return $this->belongsTo(Payrun::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function formula(): BelongsTo
    {
        return $this->belongsTo(Formula::class);
    }

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'status',
            'error',
            'details',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [];
    }

    public function filterArray(): array
    {
        return [
            [
                'name' => 'status',
            ],
        ];
    }

    public function scopeEditedManually($query)
    {
        return $query->where('edited_manually', 1);
    }

    public function payslipAdjustments(): HasMany
    {
        return $this->hasMany(PayslipAdjustment::class);
    }

    public function canUpdate(): bool
    {
        return $this->payrun?->status == PayrunStatusEnum::DRAFT->value
            || $this->status == PayslipStatusEnum::REJECTED->value;
    }

    public function canDownload(): bool
    {
        return ($this->payrun->status == PayrunStatusEnum::APPROVED->value
                || $this->payrun->status == PayrunStatusEnum::DONE->value)
            && ($this->user_id == user()->id || isAdmin() || can(PermissionEnum::PAYROLL_MANAGEMENT));
    }

    public function canShow(): bool
    {
        return isAdmin()
            || can(PermissionEnum::PAYROLL_MANAGEMENT)
            || $this->user_id == user()->id;
    }

    public function canToggleStatus(): bool
    {
        return !isAdmin()
            && $this->user_id == user()->id
            && !can(PermissionEnum::PAYROLL_MANAGEMENT)
            && $this->canUpdate();
    }

    public function calculateNetPay(): static
    {
        $cost = $this->gross_pay + $this->payslipAdjustments
                ->sum(function (PayslipAdjustment $payslipAdjustment) {
                    if ($payslipAdjustment->type == PayslipAdjustmentTypeEnum::BENEFIT->value) {
                        return $payslipAdjustment->amount;
                    } else {
                        return -($payslipAdjustment->amount);
                    }
                });

        $this->update([
            'net_pay' => $cost,
        ]);

        return $this;
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(PayslipAdjustment::class)
            ->where('type', PayslipAdjustmentTypeEnum::BENEFIT->value);
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(PayslipAdjustment::class)
            ->where('type', PayslipAdjustmentTypeEnum::DEDUCTION->value);
    }
}
