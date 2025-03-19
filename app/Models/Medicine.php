<?php

namespace App\Models;

use App\Interfaces\ActionsMustBeAuthorized;
use App\Traits\HasAbilities;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string  name
 * @property string  description
 * @property integer clinic_id
 * @property Clinic  clinic
 */
class Medicine extends Model implements ActionsMustBeAuthorized
{
    use HasFactory;
    use HasAbilities;

    protected $fillable = [
        'name',
        'description',
        'clinic_id',
    ];
    protected $casts = [

    ];

    public static function authorizedActions(): array
    {
        return [
            'manage-medicines'
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

    public function prescriptions(): BelongsToMany
    {
        return $this->belongsToMany(Prescription::class, 'medicine_prescriptions');
    }

    public function customOrders(): array
    {
        return [
            'clinic.user.first_name' => function (Builder $query, $dir) {
                return $query->join('clinics', 'clinics.id', '=', 'medicines.clinic_id')
                    ->join('users', function ($join) {
                        $join->on('users.id', '=', 'clinics.user_id');
                    })
                    ->select('medicines.*', 'users.first_name AS doctor_first_name')
                    ->orderBy('doctor_first_name', $dir);
            },
        ];
    }
}
