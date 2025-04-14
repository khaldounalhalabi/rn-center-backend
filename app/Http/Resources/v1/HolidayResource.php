<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;

/** @mixin \App\Models\Holiday */
class HolidayResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'from' => $this->from?->format('Y-m-d'),
            'to' => $this->to?->format('Y-m-d'),
            'reason' => $this->reason,
        ];
    }
}
