<?php

namespace App\Http\Resources\v1;

use App\Http\Resources\BaseResource;
use App\Models\User;
use Illuminate\Http\Request;

/** @mixin User */
class UserResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone_verified_at' => $this->phone_verified_at?->format('Y-m-d H:i:s'),
            'gender' => $this->gender,
            'formula_id' => $this->formula_id,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'permissions' => new PermissionCollection($this->whenLoaded('permissions')),
            'clinic' => new ClinicResource($this->whenLoaded('clinic')),
            'formula' => new FormulaResource($this->whenLoaded('formula')),
            $this->mergeWhen(
                $this->relationLoaded('roles'),
                fn () => [
                    'role' => $this->roles->first()->name,
                ]
            ),
            'attendance_by_date' => AttendanceLogResource::collection($this->whenLoaded('attendanceByDate')),
            'payslips' => PayslipResource::collection($this->whenLoaded('payslips')),
        ];
    }
}
