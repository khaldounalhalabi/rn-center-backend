<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;
use App\Models\Address;

/** @mixin Address */
class AddressResource extends BaseResource
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
            'city' => $this->city,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'country' => $this->country,
            'addressable_id' => $this->addressable_id,
            'addressable_type' => $this->addressable_type,
            'addressable' => $this->whenLoaded('addressable')
        ];
    }
}
