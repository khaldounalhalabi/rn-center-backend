<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;

/** @mixin \App\Models\Payslip */
class PayslipResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'payrun_id' => $this->payrun_id,
            'user_id' => $this->user_id,
            'formula_id' => $this->formula_id,
            'paid_days' => $this->paid_days,
            'gross_pay' => round($this->gross_pay, 2),
            'net_pay' => round($this->net_pay, 2),
            'status' => $this->status,
            'error' => $this->error,
            'details' => $this->details,
            'edited_manually' => $this->edited_manually,
            'payrun' => new PayrunResource($this->whenLoaded('payrun')),
            'user' => new UserResource($this->whenLoaded('user')),
            'formula' => new FormulaResource($this->whenLoaded('formula')),
            'payslip_adjustments' => PayslipAdjustmentResource::collection($this->whenLoaded('payslipAdjustments')),
            'total_benefits' => round($this->benefits_sum_amount ?? 0, 2),
            'total_deductions' => round($this->deductions_sum_amount ?? 0, 2),


            $this->mergeWhen($this->detailed, fn() => [
                'can_update' => $this->canUpdate(),
                'can_download' => $this->canDownload(),
            ]),
        ];
    }
}
