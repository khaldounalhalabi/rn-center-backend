<?php

namespace App\Models;

use App\Enums\AttendanceLogTypeEnum;
use App\Enums\ExcelColumnsTypeEnum;
use App\Enums\RolesPermissionEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'attend_at',
        'type',
        'status',
    ];

    protected $casts = [
        'attend_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function isCheckin(): bool
    {
        return $this->type == AttendanceLogTypeEnum::CHECKIN->value;
    }

    public function isCheckout(): bool
    {
        return $this->type == AttendanceLogTypeEnum::CHECKOUT->value;
    }

    public function exportable(): array
    {
        return [
            'user_id' => ExcelColumnsTypeEnum::STRING,
            'full_name' => ExcelColumnsTypeEnum::STRING,
            'role' => [
                RolesPermissionEnum::DOCTOR['role'],
                RolesPermissionEnum::SECRETARY['role']
            ],
            'attend_at' => ExcelColumnsTypeEnum::DATE_TIME,
            'type' => AttendanceLogTypeEnum::getAllValues(),
        ];
    }

    public function importExample(): array
    {
        return [
            'user_id' => ExcelColumnsTypeEnum::STRING,
            'full_name' => ExcelColumnsTypeEnum::STRING,
            'attend_at' => ExcelColumnsTypeEnum::DATE_TIME,
            'role' => [
                RolesPermissionEnum::DOCTOR['role'],
                RolesPermissionEnum::SECRETARY['role']
            ],
        ];
    }

    public function filterArray(): array
    {
        return [
            [
                'name' => 'from',
                'query' => fn(Builder|AttendanceLog $q, $value) => $q->whereDate(
                    'attend_at',
                    '>=',
                    Carbon::parse($value)->format('Y-m-d')
                )
            ],
            [
                'name' => 'to',
                'query' => fn(Builder|AttendanceLog $q, $value) => $q->whereDate(
                'attend_at',
                    '<=',
                    Carbon::parse($value)->format('Y-m-d')
                )
            ]
        ];
    }
}
