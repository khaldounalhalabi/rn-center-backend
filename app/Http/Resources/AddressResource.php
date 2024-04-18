<?php

namespace App\Http\Resources;

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
            'city_id' => $this->city_id,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'country' => $this->country,
            'addressable_id' => $this->addressable_id,
            'addressable_type' => $this->addressable_type,
            'addressable' => $this->whenLoaded('addressable'),
            "name" => $this->name,
            'map_iframe' => $this->map_iframe,
            'city' => new CityResource($this->whenLoaded('city')),
        ];
    }
}
