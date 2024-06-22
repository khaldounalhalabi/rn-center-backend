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
            'id'              => $this->id,
            'email'           => $this->email,
            'birth_date'      => $this->birth_date->format('Y-m-d'),
            'age'             => now()->diffInYears($this->birth_date),
            'gender'          => $this->gender,
            'blood_group'     => $this->blood_group,
            'is_blocked'      => $this->is_blocked,
            'tags'            => $this->tags,
            'fcm_token'       => $this->fcm_token,
            'is_archived'     => $this->is_archived,
            'first_name'      => $this->first_name,
            'middle_name'     => $this->middle_name,
            'last_name'       => $this->last_name,
            'image'           => MediaResource::collection($this->whenLoaded('media')),
            'customer'        => new CustomerResource($this->whenLoaded('customer')),
            'clinic'          => new ClinicResource($this->whenLoaded('clinic')),
            'phones'          => PhoneNumberResource::collection($this->whenLoaded('phones')),
            'address'         => new AddressResource($this->whenLoaded('address')),
            'clinicEmployees' => ClinicEmployeeResource::collection($this->whenLoaded('clinicEmployees')),
            'role'            => RoleResource::collection($this->whenLoaded('roles')),
            'permissions'     => new PermissionCollection($this->whenLoaded('permissions'))
        ];
    }
}
