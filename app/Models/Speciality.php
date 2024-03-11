<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string name
 * @property string description
 * @property string tags
 */
class Speciality extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'tags',

    ];

    protected $casts = [

    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [
            'name',
            'description',
            'tags',

        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'clinics' => [
                //add your clinics desired column to be search within
            ],

        ];
    }

    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class , 'clinic_specialities');
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
}
