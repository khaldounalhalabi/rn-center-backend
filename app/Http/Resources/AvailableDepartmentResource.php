<?php

namespace App\Http\Resources;

use App\Models\AvailableDepartment;

/** @mixin AvailableDepartment */
class AvailableDepartmentResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
        ];
    }
}
