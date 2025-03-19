<?php

namespace App\Http\Resources;

use App\Models\Customer;

/** @mixin Customer */
class CustomerResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'total_appointments' => $this->whenCounted('validAppointments'),
            'user' => new UserResource($this->whenLoaded('user')),
            'currentClinicPatientProfile' => new PatientProfileResource($this->whenLoaded('currentClinicPatientProfile')),
            'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')),
            'prescriptions' => PrescriptionResource::collection($this->whenLoaded('prescriptions')),
            'patientProfiles' => PatientProfileResource::collection($this->whenLoaded('patientProfiles')),
        ];
    }
}
