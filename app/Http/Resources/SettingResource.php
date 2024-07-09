<?php

namespace App\Http\Resources;

use App\Models\Setting;

/** @mixin Setting */
class SettingResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'value' => $this->value,

        ];
    }
}
