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
            'id' => $this->id,
            'prescription_id' => $this->prescription_id,
            'medicine_id' => $this->medicine_id,
            'dosage' => $this->dosage,
            'dose_interval' => $this->dose_interval,
            'comment' => $this->comment,
            'status' => $this->status,
            'medicine' => new MedicineResource($this->whenLoaded('medicine')),
        ];
    }
}
