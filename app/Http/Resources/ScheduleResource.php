<?php

namespace App\Http\Resources;

use App\Models\Clinic;
use App\Models\Schedule;
use App\Models\User;

/** @mixin Schedule */
class ScheduleResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $clinicId = $this->scheduleable_type === Clinic::class ? $this->scheduleable_id : null;
        $userId = $this->scheduleable_type === User::class ? $this->scheduleable_id : null;
        return [
            'id' => $this->id,
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time->format('H:i'),
            'end_time' => $this->end_time->format('H:i'),
            $this->mergeWhen($clinicId, fn() => [
                'clinic_id' => $clinicId,
                'clinic' => new ClinicResource($this->whenLoaded('scheduleable')),
            ]),
            $this->mergeWhen($userId, fn() => [
                'user_id' => $userId,
                'user' => new UserResource($this->whenLoaded('scheduleable')),
            ])
        ];
    }
}
