<?php

namespace App\Models;

use App\Casts\Translatable;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string  name
 * @property string  description
 * @property integer hospital_id
 */
class AvailableDepartment extends Model
{
    use HasFactory;
    use Translations;


    protected $fillable = [
        'name',
        'description',
        'hospital_id',

    ];

    protected $casts = [
        'name' => Translatable::class,
        'description' => Translatable::class,
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
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'hospital_id' => [
                //add your hospital_id desired column to be searched within
            ],

        ];
    }

    public function hospital(): BelongsToMany
    {
        return $this->belongsToMany(Hospital::class, 'department_hospitals');
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
