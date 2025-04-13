<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 */
class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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

    public function exportable(): array
    {
        return [
            'name',
        ];
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
