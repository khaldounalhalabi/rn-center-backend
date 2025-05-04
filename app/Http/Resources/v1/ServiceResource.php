<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\Service;

/** @mixin Service */
class ServiceResource extends BaseResource
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
            'approximate_duration' => $this->approximate_duration,
            'service_category_id' => $this->service_category_id,
            'price' => $this->price,
            'description' => $this->description,
            'clinic_id' => $this->clinic_id,
            'service_category' => new ServiceCategoryResource($this->whenLoaded('serviceCategory')),
            'clinic' => new ClinicResource($this->whenLoaded('clinic')),
            'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')),
            'icon' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
