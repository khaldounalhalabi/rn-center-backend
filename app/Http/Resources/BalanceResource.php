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
            'balance' => $this->balance,
        ];
    }
}
