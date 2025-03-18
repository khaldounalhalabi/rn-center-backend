<?php

namespace App\Http\Resources;

use App\Models\City;

/** @mixin City */
class CityResource extends BaseResource
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
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
        ];
    }
}
