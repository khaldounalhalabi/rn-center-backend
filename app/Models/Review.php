<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int      clinic_id
 * @property int      customer_id
 * @property numeric  rate
 * @property ?string  review
 * @property Clinic   clinic
 * @property Customer customer
 */
class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'customer_id',
        'rate',
        'review',

    ];

    protected $casts = [

    ];

    public function exportable(): array
    {
        return [
            'rate',
            'review',
            'clinic.name',
            'customer.mother_full_name',

        ];
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
            'review',

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

    public function canDelete(): bool
    {
        return $this->customer_id == auth()?->user()?->customer?->id;
    }

    public function canUpdate(): bool
    {
        return $this->customer_id == auth()?->user()?->customer?->id;
    }
}
