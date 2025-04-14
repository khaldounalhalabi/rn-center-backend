<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string from
 * @property Carbon to
 * @property Carbon reason
 */
class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'from',
        'to',
        'reason',
    ];

    protected $casts = [
        'from' => 'datetime',
        'to' => 'datetime',
    ];

    public function exportable(): array
    {
        return [
            'from',
            'to',
            'reason',
        ];
    }

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'reason',
            'from',
            'to',
        ];
    }

    public function filterArray(): array
    {
        return [
            [
                'name' => 'date',
                'query' => fn(Holiday|Builder $query) => $query->where('to', '<=', request('date'))
                    ->where('from', '>=', request('date'))
            ]
        ];
    }
}
