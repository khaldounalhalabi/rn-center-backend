<?php

namespace App\Http\Resources;

use App\Models\ClinicSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ClinicSubscription
 */
class ClinicSubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'start_time' => $this->start_time->format('Y-m-d H:i'),
            'end_time' => $this->end_time->format('Y-m-d H:i'),
            'status' => $this->status,
            'deduction_cost' => $this->deduction_cost,
            'subscription_id' => $this->subscription_id,
            'clinic_id' => $this->clinic_id,
            'remaining' => $this->remainingTime() . " Days Left",
            'clinic' => new ClinicResource($this->whenLoaded('clinic')),
            'subscription' => new SubscriptionResource($this->whenLoaded('subscription')),
        ];
    }
}
