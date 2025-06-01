<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\MedicalRecord;

/** @mixin MedicalRecord */
class MedicalRecordResource extends BaseResource
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
            'summary' => $this->summary,
            'diagnosis' => $this->diagnosis,
            'treatment' => $this->treatment,
            'allergies' => $this->allergies,
            'notes' => $this->notes,
            'can_delete' => $this->canDelete(),
            'can_update' => $this->canUpdate(),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'clinic' => new ClinicResource($this->whenLoaded('clinic')),
        ];
    }
}
