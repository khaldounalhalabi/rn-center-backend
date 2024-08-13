<?php

namespace App\Http\Resources;

use App\Models\BloodDonationRequest;

/** @mixin BloodDonationRequest */
class BloodDonationRequestResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'full_name'        => $this->full_name,
            'contact_phone'    => $this->contact_phone,
            'address'          => $this->address,
            'city_id'          => $this->city_id,
            'blood_group'      => $this->blood_group,
            'nearest_hospital' => $this->nearest_hospital,
            'notes'            => $this->notes,
            'can_wait_until'   => $this->can_wait_until->format('Y-m-d H:i'),
            'city'             => new CityResource($this->whenLoaded('city')),
        ];
    }
}
