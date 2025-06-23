<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\Asset;

/** @mixin Asset */
class AssetResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'serial_number' => $this->serial_number,
            'type' => $this->type,
            'quantity' => $this->quantity,
            'purchase_date' => $this->purchase_date?->format('Y-m-d'),
            'quantity_unit' => $this->quantity_unit,
            'assigned_quantity' => $this->whenAggregated('assignedUserAssets', 'quantity', 'sum', $this->assigned_user_assets_sum_quantity, 0),
            'total_quantity' => $this->whenAggregated('assignedUserAssets', 'quantity', 'sum', $this->quantity + $this->assigned_user_assets_sum_quantity, $this->quantity),
            'image' => MediaResource::collection($this->whenLoaded('media')),
            'assigned_users' => UserResource::collection($this->whenLoaded('assignedUsers')),
            'user_assets' => UserAssetResource::collection($this->whenLoaded('userAssets')),
        ];
    }
}
