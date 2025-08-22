<?php

namespace App\Models;

use App\Enums\TransactionTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string           type
 * @property numeric          amount
 * @property string           description
 * @property Carbon           date
 * @property integer          actor_id
 * @property User             actor
 * @property numeric|null     appointment_id
 * @property Appointment|null appointment
 * @property int|null         payrun_id
 * @property Payrun|null      payrun
 */
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount',
        'description',
        'date',
        'actor_id',
        'appointment_id',
        'payrun_id',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'type',
            'amount',
            'date'
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'actor' => [
                'email',
                'full_name',
                'phone'
            ]
        ];
    }

    public function exportable(): array
    {
        return [
            'type',
            'amount',
            'description',
            'date',
            'actor_id',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id', 'id');
    }

    public function filterArray(): array
    {
        return [
            [
                'name' => 'amount',
            ],
            [
                'name' => 'type'
            ],
            [
                'name' => 'date',
                'method' => 'whereDate'
            ]
        ];
    }

    public function isOutcome(): bool
    {
        return $this->type == TransactionTypeEnum::OUTCOME->value;
    }

    public function isIncome(): bool
    {
        return $this->type == TransactionTypeEnum::INCOME->value;
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function payrun(): BelongsTo
    {
        return $this->belongsTo(Payrun::class);
    }
}
