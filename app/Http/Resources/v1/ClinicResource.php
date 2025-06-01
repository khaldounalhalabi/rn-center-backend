<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\Clinic;
use Carbon\Carbon;

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
            'appointment_cost' => $this->appointment_cost,
            'user_id' => $this->user_id,
            'working_start_year' => $this->working_start_year,
            'experience_years' => now()->diffInYears(Carbon::parse($this->working_start_year . '-01-01')),
            'max_appointments' => $this->max_appointments,
            'user' => new UserResource($this->whenLoaded('user')),
            'schedules' => new ScheduleCollection($this->whenLoaded('schedules')),
            'specialities' => SpecialityResource::collection($this->whenLoaded('specialities')),
            'services' => ServiceResource::collection($this->whenLoaded('services')),
            'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')),
            $this->mergeWhen(isAdmin() || isDoctor(),
                [
                    'total_appointments' => $this->whenCounted('appointments'),
                    'today_appointments_count' => $this->whenCounted('todayAppointments'),
                    'upcoming_appointments_count' => $this->whenCounted('upcomingAppointments'),
                    'prescriptions' => PrescriptionResource::collection($this->whenLoaded('prescriptions')),
                    'medicines' => MedicineResource::collection($this->whenLoaded('medicines')),
                ]),
            'medical_records' => MedicalRecordResource::collection($this->whenLoaded('medicalRecords')),
        ];
    }
}
