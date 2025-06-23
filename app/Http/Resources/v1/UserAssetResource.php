<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\UserAsset;

/** @mixin UserAsset */
class UserAssetResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'asset_id' => $this->asset_id,
            'user_id' => $this->user_id,
            'status' => $this->status,
            'checkin_condition' => $this->checkin_condition,
            'checkout_condition' => $this->checkout_condition,
            'checkin_date' => $this->checkin_date?->format('Y-m-d H:i'),
            'checkout_date' => $this->checkout_date?->format('Y-m-d H:i'),
            'expected_return_date' => $this->expected_return_date?->format('Y-m-d'),
            'quantity' => $this->quantity,
            'asset' => AssetResource::make($this->whenLoaded('asset')),
            'user' => UserResource::make($this->whenLoaded('user')),
        ];
    }
}
