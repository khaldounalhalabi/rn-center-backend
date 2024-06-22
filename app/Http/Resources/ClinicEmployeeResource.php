<?php

namespace App\Http\Resources;

use App\Models\ClinicEmployee;

/** @mixin ClinicEmployee */
class ClinicEmployeeResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'user_id'   => $this->user_id,
            'clinic_id' => $this->clinic_id,
            'user'      => new UserResource($this->whenLoaded('user')),
            'clinic'    => new ClinicResource($this->whenLoaded('clinic')),
        ];
    }
}
