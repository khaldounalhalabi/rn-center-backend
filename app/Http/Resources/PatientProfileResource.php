<?php

namespace App\Http\Resources;

use App\Models\PatientProfile;

/** @mixin PatientProfile */
class PatientProfileResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'clinic_id' => $this->clinic_id,
            'medical_condition' => $this->medical_condition,
            'note' => $this->note,
            'other_data' => $this->other_data,
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'clinic' => new ClinicResource($this->whenLoaded('clinic')),
            'images' => MediaResource::collection($this->whenLoaded('media')),
            'last_appointment' => new AppointmentResource($this->whenLoaded('lastAppointment')),
            'appointments_count' => $this->whenCounted('appointments')
        ];
    }
}
