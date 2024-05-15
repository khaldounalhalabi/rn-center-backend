<?php

namespace App\Http\Resources;

use App\Models\Hospital;
use App\Models\PhoneNumber;
use App\Models\User;

/** @mixin PhoneNumber */
class PhoneNumberResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'label' => $this->label,
            'phoneable_id' => $this->phoneable_id,
            'phoneable_type' => $this->getPhoneableType($this->phoneable_type)
        ];
    }

    /**
     * @param class-string $className
     * @return string
     */
    private function getPhoneableType(string $className): string
    {
        return match ($className) {
            User::class => "user",
            Hospital::class => "hospital",
        };
    }
}
