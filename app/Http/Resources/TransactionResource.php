<?php

namespace App\Http\Resources;

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
            'amount' => $this->amount,
            'description' => $this->description,
            'date' => $this->date->format('Y-m-d H:i'),
            'actor_id' => $this->actor_id,
            'actor' => new UserResource($this->whenLoaded('actor')),
        ];
    }
}
