<?php

namespace App\Http\Resources;

use App\Models\Review;

/** @mixin Review */
class ReviewResource extends BaseResource
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
            'rate'        => $this->rate,
            'review'      => $this->review,
            'customer'    => new CustomerResource($this->whenLoaded('customer')),
            'clinic'      => new ClinicResource($this->whenLoaded('clinic'))
        ];
    }
}
