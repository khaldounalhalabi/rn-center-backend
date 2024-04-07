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
            ...$this->translatables(),
            'appointment_cost' => $this->appointment_cost,
            'user_id' => $this->user_id,
            "hospital_id" => $this->hospital_id,
            'working_start_year' => $this->working_start_year->format("Y-m-d"),
            'experience_years' => now()->diffInYears($this->working_start_year),
            'max_appointments' => $this->max_appointments,
            'appointment_day_range' => $this->appointment_day_range,
            "status" => $this->status,
            'about_us' => $this->about_us,
            'experience' => $this->experience,
            'work_gallery' => MediaResource::collection($this->whenLoaded('media')),
            'schedules' => ScheduleResource::collection($this->whenLoaded('schedules')),
            'clinicHolidays' => ClinicHolidayResource::collection($this->whenLoaded('clinicHolidays')),
            "specialities" => SpecialityResource::collection($this->whenLoaded('specialities')),
            'created_at' => $this->created_at->format("Y-m-d"),
            "updated_at" => $this->updated_at->format("Y-m-d"),
            "approximate_appointment_time" => $this->approximate_appointment_time,
            //TODO::add total appointments when it is done
        ];
    }

    public function translatables(): array
    {
        if ($this->translatable) {
            return [
                "name" => $this->getRawOriginal("name"),
                'user' => (new UserResource($this->whenLoaded('user')))->setTranslatable(true),
                'hospital' => (new HospitalResource($this->whenLoaded('hospital')))->setTranslatable(true),
            ];
        } else {
            return [
                "name" => $this->name,
                'user' => new UserResource($this->whenLoaded('user')),
                'hospital' => new HospitalResource($this->whenLoaded('hospital')),
            ];
        }
    }
}
