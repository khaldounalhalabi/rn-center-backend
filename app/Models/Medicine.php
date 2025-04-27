<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
        'barcode',
        'quantity',
    ];
    protected $casts = [

    ];

    public static function searchableArray(): array
    {
        return [
            'name',
            'description',
            'status',
            'barcode',
            'quantity',
        ];
    }

    public static function relationsSearchableArray(): array
    {
        return [

        ];
    }

    public function exportable(): array
    {
        return [
            'name',
            'description',
            'barcode',
            'status',
            'quantity'
        ];
    }

    public function prescriptions(): BelongsToMany
    {
        return $this->belongsToMany(Prescription::class, 'medicine_prescriptions');
    }

    public function customOrders(): array
    {
        return [

        ];
    }

    public function filterArray(): array
    {
        return [
            [
                'name' => 'status',
            ],
            [
                'name' => 'quantity'
            ]
        ];
    }
}
