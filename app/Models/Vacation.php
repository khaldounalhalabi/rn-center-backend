<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         user_id
 * @property string      from
 * @property string      to
 * @property string      reason
 * @property string      status
 * @property string|null cancellation_reason
 * @property User        user
 */
class Vacation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'from',
        'to',
        'reason',
        'status',
        'cancellation_reason',

    ];

    protected $casts = [

    ];

    public function exportable(): array
    {
        return [
            'from',
            'to',
            'reason',
            'status',
            'cancellation_reason',
            'user.first_name',

        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'reason',
            'status',
            'cancellation_reason',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'user' => [
                'full_name'
            ]
        ];
    }
}
