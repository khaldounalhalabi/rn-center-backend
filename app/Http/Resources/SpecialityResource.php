<?php

namespace App\Http\Resources;

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
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'tags'        => $this->tags,
            'clinics_count' => $this->whenCounted('clinics'),
            'clinics'     => ClinicResource::collection($this->whenLoaded('clinics')),
            'image'       => MediaResource::collection($this->whenLoaded('media'))
        ];
    }
}
