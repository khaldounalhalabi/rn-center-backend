<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

/** @mixin \App\Models\PhoneNumber */
class PhoneNumberResource extends BaseResource
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
            'phone' => $this->phone,
            'user_id' => $this->user_id,
            'hospital_id' => $this->hospital_id,
            'user' =>  new UserResource($this->whenLoaded('user')) ,
            'hospital' =>  new HospitalResource($this->whenLoaded('hospital')) ,
        ];
    }
}
