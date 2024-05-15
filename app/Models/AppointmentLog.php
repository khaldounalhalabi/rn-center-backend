<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon;

/**
 * @property integer appointment_id
 * @property string cancellation_reason
 * @property string status
 * @property integer actor_id
 * @property integer affected_id
 * @property Carbon\Carbon happen_in
 * @property Appointment appointment
 * @property User actor
 * @property User affected
 * @property string event
 */
class AppointmentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'cancellation_reason',
        'status',
        'actor_id',
        'affected_id',
        'happen_in',
        'event',
    ];

    protected $casts = [
        'happen_in' => 'datetime',

    ];

    public function exportable(): array
    {
        return [
            'cancellation_reason',
            'status',
            'happen_in',
            'appointment.id',
            'actor_id',
            'affected_id',

        ];
    }


    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
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
            'cancellation_reason',
            'status',

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

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id', 'id');
    }

    public function affected(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affected_id', 'id');
    }
}
