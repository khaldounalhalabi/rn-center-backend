<?php

namespace App\Http\Resources;

/** @mixin \App\Models\SystemOffer */
class SystemOfferResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'description'  => $this->description,
            'type'         => $this->type,
            'amount'       => $this->amount,
            'allowed_uses' => $this->allowed_uses,
            'allow_reuse'  => $this->allow_reuse,
            'clinics'      => ClinicResource::collection($this->whenLoaded('clinics')),
            'appointments' => AppointmentResource::collection($this->whenLoaded('appointments')),
            'image'        => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
