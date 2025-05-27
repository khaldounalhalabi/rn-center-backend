<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\Prescription;

/** @mixin Prescription */
class PrescriptionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'clinic_id' => $this->clinic_id,
            'customer_id' => $this->customer_id,
            'appointment_id' => $this->appointment_id,
            'other_data' => $this->other_data,
            'next_visit' => $this->next_visit?->format('Y-m-d H:i'),
            'created_at' => $this->created_at?->format('Y-m-d H:i'),
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'clinic' => ClinicResource::make($this->whenLoaded('clinic')),
            'appointment' => AppointmentResource::make($this->whenLoaded('appointment')),
            'medicines' => MedicinePrescriptionResource::collection($this->whenLoaded('medicinePrescriptions')),

            $this->mergeWhen($this->detailed, fn() => [
                'can_delete' => $this->canDelete(),
                'can_update' => $this->canUpdate(),
            ])
        ];
    }
}
