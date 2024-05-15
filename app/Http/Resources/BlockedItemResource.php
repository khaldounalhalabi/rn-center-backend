<?php

namespace App\Http\Resources;

use App\Models\BlockedItem;

/** @mixin BlockedItem */
class BlockedItemResource extends BaseResource
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
            'value' => $this->value,
        ];
    }
}
