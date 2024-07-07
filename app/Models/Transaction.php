<?php

namespace App\Models;

use App\Observers\TransactionObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string  type
 * @property numeric amount
 * @property string  description
 * @property Carbon  date
 * @property integer actor_id
 * @property User    actor
 */
#[ObservedBy([TransactionObserver::class])]
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount',
        'description',
        'date',
        'actor_id',
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
                'full_name',
                'email',
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

    /**
     * define your columns which you want to treat them as files
     * so the base repository can store them in the storage without
     * any additional files procedures
     */
    public function filesKeys(): array
    {
        return [
            //filesKeys
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
                'name' => 'date'
            ]
        ];
    }

    public function appointmentDeduction(): HasOne
    {
        return $this->hasOne(AppointmentDeduction::class, 'transaction_id', 'id');
    }
}
