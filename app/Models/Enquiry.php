<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string name
 * @property string email
 * @property string message
 * @property Carbon read_at
 */
class Enquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'message',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'name',
            'email',
            'message',
        ];
    }

    public function exportable(): array
    {
        return [
            'name',
            'email',
            'message',
            'read_at',
        ];
    }
}
