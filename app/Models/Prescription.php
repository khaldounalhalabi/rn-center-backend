<?php

namespace App\Models;

use App\Traits\HasAbilities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer  clinic_id
 * @property integer  customer_id
 * @property string   physical_information
 * @property string   problem_description
 * @property string   test
 * @property string   next_visit
 * @property Clinic   clinic
 * @property Customer customer
 */
class Prescription extends Model
{
    use HasFactory;
    use HasAbilities;

    protected $fillable = [
        'clinic_id',
        'customer_id',
        'physical_information',
        'problem_description',
        'test',
        'next_visit',
        'appointment_id',
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
            'physical_information',
            'problem_description',
            'test',
            'next_visit',
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
            'physical_information',
            'problem_description',
            'test',
            'next_visit',
            'clinic.name',
            'customer.mother_full_name',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function medicines(): BelongsToMany
    {
        return $this->belongsToMany(Medicine::class, 'medicine_prescriptions');
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

    public function medicinesData(): HasMany
    {
        return $this->hasMany(MedicinePrescription::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
