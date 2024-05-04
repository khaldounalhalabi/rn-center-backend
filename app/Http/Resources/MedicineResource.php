<?php

namespace App\Http\Resources;

use App\Models\Medicine;

/** @mixin Medicine */
class MedicineResource extends BaseResource
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
            'name' => $this->name,
            'description' => $this->description,
            'clinic_id' => $this->clinic_id,
            'clinic' => new ClinicResource($this->whenLoaded('clinic')),
        ];
    }
}
