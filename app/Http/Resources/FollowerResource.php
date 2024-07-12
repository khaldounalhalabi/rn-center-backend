<?php

namespace App\Http\Resources;

use App\Models\Follower;

/** @mixin Follower */
class FollowerResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'clinic_id'   => $this->clinic_id,
            'customer_id' => $this->customer_id,
            'clinic'      => new ClinicResource($this->whenLoaded('clinic')),
            'customer'    => new CustomerResource($this->whenLoaded('customer')),
        ];
    }
}
