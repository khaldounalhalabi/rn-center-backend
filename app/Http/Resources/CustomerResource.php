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
            'blood_group' => $this->blood_group,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'age' => round($this->birth_date?->diffInYears()),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'total_appointments' => $this->whenCounted('validAppointments'),
            'health_status' => $this->health_status,
            'notes' => $this->notes,
            'other_data' => $this->other_data,
            'user' => new UserResource($this->whenLoaded('user')),
            'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')),
            'prescriptions' => PrescriptionResource::collection($this->whenLoaded('prescriptions')),
            'attachments' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
