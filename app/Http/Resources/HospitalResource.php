<?php

namespace App\Http\Resources;

use App\Models\Hospital;

/** @mixin Hospital */
class HospitalResource extends BaseResource
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
            'status' => $this->status,
            'images' => MediaResource::collection($this->whenLoaded('media')),
            'phones' => PhoneNumberResource::collection($this->whenLoaded('phones')),
            'available_departments' => AvailableDepartmentResource::collection($this->whenLoaded('availableDepartments')),
            'address' => new AddressResource($this->whenLoaded('address')),
        ];
    }
}
