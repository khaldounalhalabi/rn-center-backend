<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

/** @mixin \App\Models\City */
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
            ...$this->translatables(),
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
        ];
    }

    public function translatables(): array
    {
        if ($this->translatable) {
            return [
                'name' => $this->getRawOriginal("name"),
            ];
        }
        return [
            'name' => $this->name,
        ];
    }
}
