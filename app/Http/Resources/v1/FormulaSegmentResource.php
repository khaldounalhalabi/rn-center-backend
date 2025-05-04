<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;

/** @mixin \App\Models\FormulaSegment */
class FormulaSegmentResource extends BaseResource
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
            'segment' => $this->segment,
            'formula_id' => $this->formula_id,
            'formula' => new FormulaResource($this->whenLoaded('formula')),
        ];
    }
}
