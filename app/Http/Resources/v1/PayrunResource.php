<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\Payrun;

/** @mixin Payrun */
class PayrunResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'should_delivered_at' => $this->should_delivered_at?->format('Y-m-d'),
            'payment_date' => $this->payment_date,
            'payment_cost' => round($this->payment_cost, 2),
            'period' => $this->period,
            'from' => $this->from?->format('Y-m-d'),
            'to' => $this->to?->format('Y-m-d'),
            'has_errors' => $this->has_errors,
            'processed_at' => $this->processed_at?->format('Y-m-d H:i:s'),
            'processed_users_count' => $this->whenCounted('processedUsers'),
            'excluded_users_count' => $this->whenCounted('excludedUsers'),
            'payslips' => PayslipResource::collection($this->whenLoaded('payslips')),
            'processed_users' => UserResource::collection($this->whenLoaded('processedUsers')),
            'excluded_users' => UserResource::collection($this->whenLoaded('excludedUsers')),


            $this->mergeWhen($this->detailed, fn() => [
                'can_update' => $this->canUpdate(),
                'can_delete' => $this->canDelete(),
            ]),
        ];
    }
}
