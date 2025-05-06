<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\PayslipAdjustment;

/** @mixin PayslipAdjustment */
class PayslipAdjustmentResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'payslip_id' => $this->payslip_id,
            'amount' => round($this->amount, 2),
            'reason' => $this->reason,
            'type' => $this->type,
            'payslip' => new PayslipResource($this->whenLoaded('payslip')),
        ];
    }
}
