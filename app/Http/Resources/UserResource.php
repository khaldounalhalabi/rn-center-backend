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
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'birth_date' => $this->birth_date,
            'gender' => $this->gender,
            'blood_group' => $this->blood_group,
            'is_blocked' => $this->is_blocked,
            'tags' => $this->tags,
            'fcm_token' => $this->fcm_token,
            'is_archived' => $this->is_archived,
            'image' => MediaResource::collection($this->whenLoaded('media')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'clinics' => new ClinicResource($this->whenLoaded('clinic')),
            'phoneNumbers' => PhoneNumberResource::collection($this->whenLoaded('phoneNumbers')),
        ];
    }
}
