<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer  customer_id
 * @property integer  clinic_id
 * @property string   medical_condition
 * @property string   note
 * @property string   other_data
 * @property Customer customer
 * @property Clinic   clinic
 */
class PatientProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'clinic_id',
        'medical_condition',
        'note',
        'other_data',
    ];

    protected $casts = [

    ];

    public function exportable(): array
    {
        return [
            'medical_condition',
            'note',
            'other_data',
            'customer.mother_full_name',
            'clinic.name',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function clinic()
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
            'medical_condition',
            'note',
            'other_data',
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'clinic'        => [
                'name'
            ],
            'clinic.user'   => [
                'name'
            ],
            'customer.user' => [
                'full_name'
            ]
        ];
    }


}
