<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

/** @mixin \App\Models\AvailableDepartment */
class AvailableDepartmentResource extends BaseResource
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
            'hospital_id' => $this->hospital_id,
            'hospital' =>  new HospitalResource($this->whenLoaded('hospital')) ,
        ];
    }
}
