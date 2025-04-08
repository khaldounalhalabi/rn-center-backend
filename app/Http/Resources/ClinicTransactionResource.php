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
            'id' => $this->id,
            'amount' => $this->amount,
            'after_balance' => $this->after_balance,
            'before_balance' => $this->before_balance,
            'appointment_id' => $this->appointment_id,
            'type' => $this->type,
            'clinic_id' => $this->clinic_id,
            'notes' => $this->notes,
            'status' => $this->status,
            'date' => $this->date->format('Y-m-d H:i'),
            'appointment' => new AppointmentResource($this->whenLoaded('appointment')),
            'clinic' => new ClinicResource($this->whenLoaded('clinic')),
        ];
    }
}
