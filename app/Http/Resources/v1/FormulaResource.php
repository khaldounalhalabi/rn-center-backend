<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\Formula;

/** @mixin Formula */
class FormulaResource extends BaseResource
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
            'name' => $this->name,
            'formula' => $this->formula,
            'slug' => $this->slug,
            'template' => $this->template,
            'formula_segments' => FormulaSegmentResource::collection($this->whenLoaded('formulaSegments')),
            'formula_variables' => FormulaVariableResource::collection($this->whenLoaded('formulaVariables')),
            'payslips' => PayslipResource::collection($this->whenLoaded('payslips')),
        ];
    }
}
