<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string  balanceable_type
 * @property numeric balanceable_id
 * @property numeric balance
 */
class Balance extends Model
{
    use HasFactory;

    protected $fillable = [
        'balanceable_type',
        'balanceable_id',
        'balance',
        'note'
    ];

    protected $casts = [

    ];

    public function exportable(): array
    {
        return [
            'balanceable_type',
            'balanceable_id',
            'balance',
            'note'
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

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'balanceable_type',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [

        ];
    }

    public function balanceable(): MorphTo
    {
        return $this->morphTo('balanceable', 'balanceable_type', 'balanceable_id');
    }
}
