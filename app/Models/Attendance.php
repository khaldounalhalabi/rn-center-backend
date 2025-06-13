<?php

namespace App\Models;

use App\Enums\AttendanceLogStatusEnum;
use App\Enums\RolesPermissionEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Models\Role;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function onTimeLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class)
            ->where('status', AttendanceLogStatusEnum::ON_TIME->value);
    }

    public function lateLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class)
            ->where('status', AttendanceLogStatusEnum::LATE->value);
    }

    public function total(): HasMany
    {
        return $this->hasMany(AttendanceLog::class)
            ->selectRaw('user_id')
            ->groupBy('user_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'attendance_logs', 'attendance_id', 'user_id')
            ->whereHas('roles', function (Builder|Role $role) {
                $role->whereIn('name', [
                    RolesPermissionEnum::SECRETARY['role'],
                    RolesPermissionEnum::DOCTOR['role'],
                ]);
            });
    }
}
