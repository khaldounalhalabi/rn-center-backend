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
            'id'              => $this->id,
            'user_id'         => $this->user_id,
            'user'            => new UserResource($this->whenLoaded('user')),
            'appointments'    => AppointmentResource::collection($this->whenLoaded('appointments')),
            'prescriptions'   => PrescriptionResource::collection($this->whenLoaded('prescriptions')),
            'patientProfiles' => PatientProfileResource::collection($this->whenLoaded('patientProfiles')),
        ];
    }
}
