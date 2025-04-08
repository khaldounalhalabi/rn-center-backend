<?php

namespace App\Http\Resources;

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
            'email' => $this->email,
            'phone' => $this->phone,
            'phone_verified_at' => $this->phone_verified_at?->format('Y-m-d H:i:s'),
            'gender' => $this->gender,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'permissions' => new PermissionCollection($this->whenLoaded('permissions')),
            'clinic' => new ClinicResource($this->whenLoaded('clinic')),
            'clinicEmployees' => ClinicEmployeeResource::collection($this->whenLoaded('clinicEmployees')),
            $this->mergeWhen($this->relationLoaded('roles'),
                fn() => [
                    'role' => $this->roles->first()->name,
                ]),
        ];
    }
}
