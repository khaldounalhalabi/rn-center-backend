<?php

namespace App\Http\Resources;

use App\Models\Balance;

/** @mixin Balance */
class BalanceResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'balanceable_type' => $this->balanceable_type,
            'balanceable_id' => $this->balanceable_id,
            'balance' => $this->balance,

        ];
    }
}
