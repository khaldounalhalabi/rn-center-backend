<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         customer_id
 * @property int         clinic_id
 * @property null|string summary
 * @property null|string diagnosis
 * @property null|string treatment
 * @property null|string allergies
 * @property null|string notes
 * @property Customer    customer
 * @property Clinic      clinic
 */
class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'clinic_id',
        'summary',
        'diagnosis',
        'treatment',
        'allergies',
        'notes',
    ];

    protected $casts = [

    ];

    public function exportable(): array
    {
        return [
            'summary',
            'diagnosis',
            'treatment',
            'allergies',
            'notes',
            'customer.mother_full_name',
            'clinic.name',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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
            'summary',
            'diagnosis',
            'treatment',
            'allergies',
            'notes',
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

    public function canUpdate(): bool
    {
        return $this->clinic_id == clinic()?->id;
    }

    public function canDelete(): bool
    {
        return $this->clinic_id == clinic()?->id;
    }
}
