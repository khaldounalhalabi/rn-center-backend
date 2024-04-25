<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;
use App\Models\Appointment;

/** @mixin Appointment */
class AppointmentResource extends BaseResource
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
            'customer_id' => $this->customer_id,
            'clinic_id' => $this->clinic_id,
            'note' => $this->note,
            'service_id' => $this->service_id,
            'extra_fees' => $this->extra_fees,
            'total_cost' => $this->total_cost,
            'type' => $this->type,
            'date' => $this->date->format('Y-m-d'),
            'from' => $this->from->format('H:i'),
            'to' => $this->to->format('H:i'),
            'status' => $this->status,
            'device_type' => $this->device_type,
            'appointment_sequence' => $this->appointment_sequence,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'clinic' => new ClinicResource($this->whenLoaded('clinic')),
            'service' => new ServiceResource($this->whenLoaded('service')),
        ];
    }
}
