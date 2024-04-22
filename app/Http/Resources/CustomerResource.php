<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

/** @mixin \App\Models\Customer */
class CustomerResource extends BaseResource
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
            'mother_full_name' => $this->mother_full_name,
            'medical_condition' => $this->medical_condition,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')),
        ];
    }
}
