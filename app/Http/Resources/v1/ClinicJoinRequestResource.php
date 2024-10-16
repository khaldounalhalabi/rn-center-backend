<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Http\Resources\CityResource;
use App\Models\ClinicJoinRequest;

/** @mixin ClinicJoinRequest */
class ClinicJoinRequestResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'doctor_name'  => $this->doctor_name,
            'clinic_name'  => $this->clinic_name,
            'phone_number' => $this->phone_number,
            'city_id'      => $this->city_id,
            'city' => new CityResource($this->whenLoaded('city')),
        ];
    }
}
