<?php

namespace App\Http\Resources;

use App\Models\ClinicTransaction;

/** @mixin ClinicTransaction */
class ClinicTransactionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'amount'         => $this->amount,
            'appointment_id' => $this->appointment_id,
            'type'           => $this->type,
            'clinic_id'      => $this->clinic_id,
            'notes'          => $this->notes,
            'status'         => $this->status,
            'date'           => $this->date->format('Y-m-d'),
            'appointment'    => new AppointmentResource($this->whenLoaded('appointment')),
            'clinic'         => new ClinicResource($this->whenLoaded('clinic')),
        ];
    }
}
