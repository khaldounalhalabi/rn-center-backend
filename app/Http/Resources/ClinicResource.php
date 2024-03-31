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
            'working_start_year' => $this->working_start_year->format("Y-m-d"),
            'experience_years' => now()->diffInYears($this->working_start_year),
            'max_appointments' => $this->max_appointments,
            'appointment_day_range' => $this->appointment_day_range,
            'about_us' => $this->about_us,
            'experience' => $this->experience,
            'work_gallery' => MediaResource::collection($this->whenLoaded('media')),
            'user' => new UserResource($this->whenLoaded('user')),
            'hospital' => new HospitalResource($this->whenLoaded('hospital')),
            'schedules' => ScheduleResource::collection($this->whenLoaded('schedules')),
            'specialities' => SpecialityResource::collection($this->whenLoaded('specialities')),
            'clinicHolidays' => ClinicHolidayResource::collection($this->whenLoaded('clinicHolidays')),
            'created_at' => $this->created_at->format("Y-m-d") ,
            "updated_at" => $this->updated_at->format("Y-m-d") ,
            //TODO::add total appointments when it is done
        ];
    }
}
