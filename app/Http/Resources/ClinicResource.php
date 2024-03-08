<?php

namespace App\Http\Resources;

use App\Models\Clinic;

/** @mixin Clinic */
class ClinicResource extends BaseResource
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
            'name' => $this->name,
            'appointment_cost' => $this->appointment_cost,
            'user_id' => $this->user_id,
            'working_start_year' => $this->working_start_year,
            'max_appointments' => $this->max_appointments,
            'appointment_day_range' => $this->appointment_day_range,
            'about_us' => $this->about_us,
            'experience' => $this->experience,
            'work_gallery' => MediaResource::collection($this->whenLoaded('media')),
            'user' => new UserResource($this->whenLoaded('user')),
            'schedules' => ScheduleResource::collection($this->whenLoaded('schedules')),
            'specialities' => SpecialityResource::collection($this->whenLoaded('specialities')),
        ];
    }
}
