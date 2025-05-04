<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\AppointmentLog;

/** @mixin AppointmentLog */
class AppointmentLogResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'appointment_id' => $this->appointment_id,
            'cancellation_reason' => $this->cancellation_reason,
            'status' => $this->status,
            'actor_id' => $this->actor_id,
            'affected_id' => $this->affected_id,
            'happen_in' => $this->happen_in->format('Y-m-d H:i:s'),
            'event' => $this->event,
            'appointment' => new AppointmentResource($this->whenLoaded('appointment')),
            'actor' => new UserResource($this->whenLoaded('actor')),
        ];
    }
}
