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
            'email' => $this->email,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'age' => $this->birth_date ? now()->diffInYears($this->birth_date) : null,
            'gender' => $this->gender,
            'blood_group' => $this->blood_group,
            'fcm_token' => $this->fcm_token,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'address' => new AddressResource($this->whenLoaded('address')),
            'permissions' => new PermissionCollection($this->whenLoaded('permissions')),
            'image' => MediaResource::collection($this->whenLoaded('media')),
            'phones' => PhoneNumberResource::collection($this->whenLoaded('phones')),
            'clinicEmployees' => ClinicEmployeeResource::collection($this->whenLoaded('clinicEmployees')),
            'role' => RoleResource::collection($this->whenLoaded('roles')),
            $this->mergeWhen($this->relationLoaded('clinic') || $this->relationLoaded('clinicEmployee.clinic'), fn() => [
                'clinic' => new ClinicResource($this->isDoctor()
                    ? $this->clinic
                    : ($this->clinicEmployee?->clinic)),
            ])
        ];
    }
}
