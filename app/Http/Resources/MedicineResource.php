<?php

namespace App\Http\Resources;

use App\Models\Medicine;

/** @mixin Medicine */
class MedicineResource extends BaseResource
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
            'description' => $this->description,
            'status' => $this->status,
            'barcode' => $this->barcode,
            'quantity' => $this->quantity,
        ];
    }
}
