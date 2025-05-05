<?php

namespace App\Models;

use App\Casts\Translatable;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property \App\Serializers\Translatable name
 * @property string                        slug
 * @property \App\Serializers\Translatable description
 */
class FormulaVariable extends Model
{
    use HasFactory;
    use Sluggable;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected $casts = [
        'name' => Translatable::class,
        'description' => Translatable::class
    ];

    public function exportable(): array
    {
        return [
            'name',
            'slug',
            'description',
        ];
    }

    public function formulas(): BelongsToMany
    {
        return $this->belongsToMany(Formula::class);
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
            'slug',
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

        ];
    }

    public function sluggable(): array
    {
        return [
            [
                'col' => 'name',
                'slug_col' => 'slug',
                'separator' => '_',
            ],
        ];
    }
}
