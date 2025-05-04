<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\Transaction;

/** @mixin Transaction */
class TransactionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => round($this->amount, 2),
            'description' => $this->description,
            'date' => $this->date->format('Y-m-d H:i'),
            'actor_id' => $this->actor_id,
            'appointment_id' => $this->appointment_id,
            'actor' => new UserResource($this->whenLoaded('actor')),
            'appointment' => new AppointmentResource($this->whenLoaded('appointment')),
        ];
    }
}
