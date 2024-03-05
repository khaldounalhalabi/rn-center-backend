<?php

namespace App\Http\Resources;

use App\Models\Schedule;

/** @mixin Schedule */
class ScheduleResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'clinic_id' => $this->clinic_id,
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'hospital_id' => $this->hospital_id,
            'clinic' =>  new ClinicResource($this->whenLoaded('clinic')) ,
        ];
    }
}
