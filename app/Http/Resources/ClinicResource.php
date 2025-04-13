<?php

namespace App\Http\Resources;

use App\Models\Clinic;

/** @mixin Clinic */
class ClinicResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'appointment_cost' => $this->appointment_cost,
            'user_id' => $this->user_id,
            'working_start_year' => $this->working_start_year->format('Y-m-d'),
            'experience_years' => now()->diffInYears($this->working_start_year),
            'max_appointments' => $this->max_appointments,
            'user' => new UserResource($this->whenLoaded('user')),
            'schedules' => new ScheduleCollection($this->whenLoaded('schedules')),
            'specialities' => SpecialityResource::collection($this->whenLoaded('specialities')),
            'services' => ServiceResource::collection($this->whenLoaded('services')),
            'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')),

            $this->mergeWhen(isAdmin() || isDoctor(), [
                'total_appointments' => $this->whenCounted('appointments'),
                'today_appointments_count' => $this->whenCounted('todayAppointments'),
                'upcoming_appointments_count' => $this->whenCounted('upcomingAppointments'),
                'patientProfiles' => PatientProfileResource::collection($this->whenLoaded('patientProfiles')),
                'prescriptions' => PrescriptionResource::collection($this->whenLoaded('prescriptions')),
                'medicines' => MedicineResource::collection($this->whenLoaded('medicines')),
            ]),
        ];
    }
}
