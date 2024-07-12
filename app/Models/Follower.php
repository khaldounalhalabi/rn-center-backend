<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int clinic_id
 * @property int customer_id
 * @property Clinic clinic
 * @property Customer customer
 */
class Follower extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'customer_id',
    ];

    protected $casts = [

    ];

    public function exportable(): array
    {
        return [
            'clinic.name',
            'customer.mother_full_name',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
