<?php

namespace App\Http\Resources;

use App\Models\Appointment;

/** @mixin Appointment */
class AppointmentResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'                    => $this->id,
            'customer_id'           => $this->customer_id,
            'clinic_id'             => $this->clinic_id,
            'note'                  => $this->note,
            'service_id'            => $this->service_id,
            'extra_fees'            => $this->extra_fees,
            'total_cost'            => $this->total_cost,
            'discount'              => $this->discount,
            'type'                  => $this->type,
            'date'                  => $this->date?->format('Y-m-d'),
            'status'                => $this->status,
            'device_type'           => $this->device_type,
            'appointment_sequence'  => $this->appointment_sequence,
            'remaining_time'        => $this->remaining_time,
            'cancellation_reason'   => $this->whenLoaded('cancelLog')?->cancellation_reason,
            'customer'              => new CustomerResource($this->whenLoaded('customer')),
            'clinic'                => new ClinicResource($this->whenLoaded('clinic')),
            'service'               => new ServiceResource($this->whenLoaded('service')),
            'clinic_transaction'    => new ClinicTransactionResource($this->whenLoaded('clinicTransactions')),
            'appointment_deduction' => new AppointmentDeductionResource($this->whenLoaded('appointmentDeduction')),
            'appointmentLogs'       => AppointmentLogResource::collection($this->whenLoaded('appointmentLogs')),
            'system_offers'         => SystemOfferResource::collection($this->whenLoaded('systemOffers')),
            'offers'                => OfferResource::collection($this->whenLoaded('offers')),
        ];
    }
}
