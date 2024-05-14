<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property string type
 * @property string value
 */
class BlockedItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'value',

    ];

    protected $casts = [

    ];

    public function exportable(): array
    {
        return [
            'type',
            'value',
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
            'type',
            'value',
        ];
    }
}
