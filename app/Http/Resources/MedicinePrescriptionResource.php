<?php

namespace App\Http\Resources;

use App\Models\MedicinePrescription;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin MedicinePrescription */
class MedicinePrescriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'dosage'        => $this->dosage,
            'duration'      => $this->duration,
            'time'          => $this->time,
            'dose_interval' => $this->dose_interval,
            'comment'       => $this->comment,

            'prescription_id' => $this->prescription_id,
            'medicine_id'     => $this->medicine_id,

            'medicine'     => new MedicineResource($this->whenLoaded('medicine')),
            'prescription' => new PrescriptionResource($this->whenLoaded('prescription')),
        ];
    }
}
