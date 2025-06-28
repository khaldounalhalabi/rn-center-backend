<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;

/** @mixin \App\Models\PatientStudy */
class PatientStudyResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'patient_uuid' => $this->patient_uuid,
            'customer_id' => $this->customer_id,
            'study_uuid' => $this->study_uuid,
            'study_uid' => $this->study_uid,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'study_date' => $this->study_date?->format('Y-m-d H:i:s'),
            'title' => $this->title,
        ];
    }
}
