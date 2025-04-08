<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer id
 * @property string  phone
 * @property string  code
 * @property Carbon  valid_until
 * @property boolean is_active
 */
class VerificationCode extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'phone',
        'code',
        'valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'valid_until' => 'datetime',
        ];
    }
}
