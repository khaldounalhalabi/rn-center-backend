<?php

namespace App\Models;

use App\Casts\Translatable;
use App\Traits\Translations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 */
class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',

    ];

    protected $casts = [
        'name' => Translatable::class,

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

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
