<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int payslip_id
 * @property numeric amount
 * @property null|string reason
 * @property string type
 * @property Payslip payslip
 */
class PayslipAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payslip_id',
        'amount',
        'reason',
        'type',

    ];

    protected $casts = [];

    public function payslip()
    {
        return $this->belongsTo(Payslip::class);
    }

    public function canDelete(): bool
    {
        return $this->payslip->canUpdate();
    }
}
