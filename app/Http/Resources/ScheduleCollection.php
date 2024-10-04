<?php

namespace App\Http\Resources;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

/**
 * @mixin Schedule
 * @property Collection<Schedule> $collection
 */
class ScheduleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            ...$this->collection->groupBy('day_of_week')->toArray(),
            'appointment_gap' => $this->collection->first()->appointment_gap,
        ];
    }
}
