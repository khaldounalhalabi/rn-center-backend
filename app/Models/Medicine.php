<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string name
 * @property string description
 * @property integer clinic_id
 * @property Clinic clinic
 */
class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'clinic_id',
    ];

    protected $casts = [

    ];

    public function exportable(): array
    {
        return [
            'name',
            'description',
            'clinic.name',
        ];
    }


    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
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
            'name',
            'description',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'clinic' => [
                'name'
            ],
            'clinic.user' => [
                'full_name',
                'first_name',
                'last_name'
            ]
        ];
    }
}
