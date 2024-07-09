<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string label
 * @property string value
 */
class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'value',
    ];

    public function exportable(): array
    {
        return [
            'label',
            'value',
        ];
    }

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'label',
            'value',
        ];
    }
}
