<?php

namespace App\Http\Resources;

use App\Models\ClinicHoliday;

/** @mixin ClinicHoliday */
class ClinicHolidayResource extends BaseResource
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
            'clinic_id' => $this->clinic_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'reason' => $this->reason,
            'clinic' => new ClinicResource($this->whenLoaded('clinic')),
        ];
    }
}
