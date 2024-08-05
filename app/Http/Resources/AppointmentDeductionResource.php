<?php

namespace App\Http\Resources;

use App\Models\AppointmentDeduction;

/** @mixin AppointmentDeduction */
class AppointmentDeductionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'                    => $this->id,
            'amount'                => $this->amount,
            'status'                => $this->status,
            'clinic_transaction_id' => $this->clinic_transaction_id,
            'appointment_id'        => $this->appointment_id,
            'clinic_id'             => $this->clinic_id,
            'date'                  => $this->date->format('Y-m-d H:i'),
            'clinic_transaction'    => new ClinicTransactionResource($this->whenLoaded('clinicTransaction')),
            'appointment'           => new AppointmentResource($this->whenLoaded('appointment')),
            'clinic'                => new ClinicResource($this->whenLoaded('clinic')),
            $this->mergeWhen(auth()->user()?->isClinic(), [
                'type' => $this->amount > 0 ? "Debt To The System" : "Debt To Me",
            ]),
            $this->mergeWhen(auth()->user()?->isAdmin(), [
                'transaction_id' => $this->transaction_id,
                'transaction'    => $this->whenLoaded('transaction'),
            ]),
        ];
    }
}
