<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

/** @mixin \App\Models\Prescription */
class PrescriptionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'clinic_id' => $this->clinic_id,
            'customer_id' => $this->customer_id,
            'physical_information' => $this->physical_information,
            'problem_description' => $this->problem_description,
            'test' => $this->test,
            'next_visit' => $this->next_visit,
            'clinic' => new ClinicResource($this->whenLoaded('clinic')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'medicines' => MedicineResource::collection($this->whenLoaded('medicines')),
            'medicines_data' => MedicinePrescriptionResource::collection($this->whenLoaded('medicinesData')),
        ];
    }
}
