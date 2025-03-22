<?php

namespace App\Models;

use App\Interfaces\ActionsMustBeAuthorized;
use App\Traits\HasAbilities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int    user_id
 * @property int    clinic_id
 * @property User   user
 * @property Clinic clinic
 */
class ClinicEmployee extends Model implements ActionsMustBeAuthorized
{
    use HasFactory;
    use HasAbilities;

    protected $fillable = [
        'user_id',
        'clinic_id',
    ];
    protected $casts = [

    ];

    public static function authorizedActions(): array
    {
        return [
            'manage-employees'
        ];
    }

    /**
     * add your relations and their searchable columns,
     * so you can search within them in the index method
     */
    public static function relationsSearchableArray(): array
    {
        return [
            'user' => [
                'email'
            ],
        ];
    }

    public function exportable(): array
    {
        return [
            'user.first_name',
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

    public function canDelete(): bool
    {
        return (
            ($this->clinic_id == auth()?->user()?->getClinicId() && $this->clinic->isAvailable())
            || (auth()->user()?->id == $this->user_id && $this->clinic->isAvailable())
            || auth()->user()?->isAdmin()
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canShow(): bool
    {
        return (
            ($this->clinic_id == auth()?->user()?->getClinicId() && $this->clinic->isAvailable())
            || (auth()->user()?->id == $this->user_id && $this->clinic->isAvailable())
            || auth()->user()?->isAdmin()
        );
    }

    public function canUpdate(): bool
    {
        return (
            ($this->clinic_id == auth()?->user()?->getClinicId() && $this->clinic->isAvailable())
            || (auth()->user()?->id == $this->user_id && $this->clinic->isAvailable())
            || auth()->user()?->isAdmin()
        );
    }
}
