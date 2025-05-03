<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\Attendance;

/** @mixin Attendance */
class AttendanceResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'date' => $this->date?->format('Y-m-d'),
            $this->mergeWhen($this->detailed, fn() => [
                'overtime_count' => $this->whenCounted('overtimeLogs'),
                'absent_count' => max(0, $this->users_count - $this->total->count()),
                'late_count' => $this->whenCounted('lateLogs'),
                'total_count' => $this->whenCounted('attendanceLogs'),
                'on_time_count' => $this->whenCounted('onTimeLogs'),
            ])
        ];
    }
}
