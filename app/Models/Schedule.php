<?php

namespace App\Models;

use App\Enums\RolesPermissionEnum;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Permission\Models\Role;

/**
 * @property string                    day_of_week
 * @property DateTime                  start_time
 * @property DateTime                  end_time
 * @property int                       scheduleable_id
 * @property class-string<User|Clinic> scheduleable_type
 */
class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_of_week',
        'start_time',
        'end_time',
        'appointment_gap',
        'scheduleable_id',
        'scheduleable_type',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * add your searchable columns, so you can search within them in the
     * index method
     */
    public static function searchableArray(): array
    {
        return [

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

    public function filterArray(): array
    {
        return [

        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'scheduleable_id', 'id')
            ->where('scheduleable_type', Clinic::class);
    }

    public function secretary(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scheduleable_id', 'id')
            ->where('scheduleable_type', User::class)
            ->whereHas('roles', function (Role $query) {
                $query->where('name', RolesPermissionEnum::SECRETARY['role']);
            });
    }

    public function scheduleable(): MorphTo
    {
        return $this->morphTo();
    }
}
