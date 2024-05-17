<?php

namespace App\Http\Resources;

/** @mixin \App\Models\Enquiry */
class EnquiryResource extends BaseResource
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
            'email' => $this->email,
            'message' => $this->message,
            'read_at' => $this->read_at?->format('Y-m-d H:i:s'),
        ];
    }
}
