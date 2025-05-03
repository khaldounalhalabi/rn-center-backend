<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;

/** @mixin AttendanceLog */
class AttendanceLogResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'attendance_id' => $this->attendance_id,
            'user_id' => $this->user_id,
            'attend_at' => $this->attend_at?->format('Y-m-d H:i:s'),
            'type' => $this->type,
            'status' => $this->status,
        ];
    }
}
