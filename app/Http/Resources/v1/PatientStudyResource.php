<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use Illuminate\Support\Facades\URL;

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
            'title' => $this->title,
            'uuid' => $this->uuid,
            'patient_uuid' => $this->patient_uuid,
            'customer_id' => $this->customer_id,
            'study_uuid' => $this->study_uuid,
            'study_uid' => $this->study_uid,
            'study_date' => $this->study_date?->format('Y-m-d H:i:s'),
            'available_modes' => $this->available_modes,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
        ];
    }
}
