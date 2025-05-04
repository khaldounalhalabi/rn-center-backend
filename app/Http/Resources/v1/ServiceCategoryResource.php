<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\ServiceCategory;

/** @mixin ServiceCategory */
class ServiceCategoryResource extends BaseResource
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
            'services' => ServiceResource::collection($this->whenLoaded('services')),
        ];
    }
}
