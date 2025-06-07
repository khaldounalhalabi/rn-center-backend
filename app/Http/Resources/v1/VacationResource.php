<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\Vacation;

/** @mixin Vacation */
class VacationResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'from' => $this->from,
            'to' => $this->to,
            'reason' => $this->reason,
            'status' => $this->status,
            'cancellation_reason' => $this->cancellation_reason,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
