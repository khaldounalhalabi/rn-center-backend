<?php

namespace App\Http\Resources;

use App\Models\Subscription;

/** @mixin Subscription */
class SubscriptionResource extends BaseResource
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
            'description' => $this->description,
            'period' => $this->period,
            'allow_period' => $this->allow_period,
            'cost' => $this->cost,
            'clinics' => ClinicResource::collection($this->whenLoaded('clinics')),
        ];
    }
}
