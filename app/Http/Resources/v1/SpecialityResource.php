<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\Speciality;

/** @mixin Speciality */
class SpecialityResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'clinics_count' => $this->whenCounted('clinics'),
            'clinics' => ClinicResource::collection($this->whenLoaded('clinics')),
            'image' => MediaResource::collection($this->whenLoaded('media'))
        ];
    }
}
