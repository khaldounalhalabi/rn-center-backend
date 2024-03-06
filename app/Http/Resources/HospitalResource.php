<?php

namespace App\Http\Resources;

/** @mixin \App\Models\Hospital */
class HospitalResource extends BaseResource
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
            //TODO:fix
//            'available_departments' => $this->available_departments,
            'images' => MediaResource::collection($this->whenLoaded('media')),
            'phoneNumbers' => PhoneNumberResource::collection($this->whenLoaded('phoneNumbers')),
        ];
    }
}
