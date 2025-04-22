<?php

namespace App\Http\Resources;

use App\Models\Appointment;
use Exception;

/** @mixin Appointment */
class AppointmentResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     * @throws Exception
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'clinic_id' => $this->clinic_id,
            'service_id' => $this->service_id,
            'note' => $this->note,
            'extra_fees' => $this->extra_fees,
            'total_cost' => $this->total_cost,
            'type' => $this->type,
            'date_time' => $this->date_time?->format('Y-m-d H:i'),
            'status' => $this->status,
            'appointment_sequence' => $this->appointment_sequence,
            'remaining_time' => $this->remaining_time?->forHumans(),
            'discount' => $this->discount,
            'service' => ServiceResource::make($this->whenLoaded('service')),
            'clinic' => ClinicResource::make($this->whenLoaded('clinic')),
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
        ];
    }
}
