<?php

namespace App\Http\Resources;

use App\Models\Clinic;
use App\Models\Schedule;

/** @mixin Schedule */
class ScheduleResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $loaded = $this->schedulable_type == Clinic::class
            ? [
                'clinic_id' => $this->schedulable_id,
                'clinic'    => new ClinicResource($this->whenLoaded('schedulable')),
            ] : [
                'hospital_id' => $this->schedulable_id,
                'clinic'      => new HospitalResource($this->whenLoaded('schedulable')),
            ];

        return [
            'id'              => $this->id,
            'day_of_week'     => $this->day_of_week,
            'start_time'      => $this->start_time->format('H:i'),
            'end_time'        => $this->end_time->format('H:i'),
            'appointment_gap' => $this->appointment_gap,
            ...$loaded
        ];
    }
}
