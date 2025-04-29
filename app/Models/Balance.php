<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property numeric balance
 */
class Balance extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance',
    ];

    public function exportable(): array
    {
        return [
            'balance',
        ];
    }
}
