<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int          asset_id
 * @property int          user_id
 * @property string       status
 * @property numeric      checkin_condition
 * @property null|numeric checkout_condition
 * @property Carbon       checkin_date
 * @property null|Carbon  checkout_date
 * @property Asset        asset
 * @property User         user
 * @property double       quantity
 * @property Carbon|null  expected_return_date
 */
class UserAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'user_id',
        'status',
        'checkin_condition',
        'checkout_condition',
        'checkin_date',
        'checkout_date',
        'quantity',
        'expected_return_date',
    ];

    protected $casts = [
        'checkin_date' => 'datetime',
        'checkout_date' => 'datetime',
        'expected_return_date' => 'datetime',
    ];

    public function exportable(): array
    {
        return [
            'status',
            'checkin_condition',
            'checkout_condition',
            'checkin_date',
            'checkout_date',
            'asset.name',
            'user.full_name',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
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
            'status',
        ];
    }

    public function filterArray(): array
    {
        return [
            [
                'name' => 'status'
            ]
        ];
    }
}
