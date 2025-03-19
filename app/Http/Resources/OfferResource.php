<?php

namespace App\Http\Resources;

use App\Models\Offer;

/** @mixin Offer */
class OfferResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'value' => $this->value,
            'note' => $this->note,
            'start_at' => $this->start_at->format('Y-m-d'),
            'end_at' => $this->end_at->format('Y-m-d'),
            'is_active' => $this->is_active,
            'type' => $this->type,
            'clinic_id' => $this->clinic_id,
            'clinic' => new ClinicResource($this->whenLoaded('clinic')),
            'image' => MediaResource::collection($this->whenLoaded('media'))
        ];
    }
}
