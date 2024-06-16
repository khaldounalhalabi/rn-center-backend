<?php

namespace App\Http\Requests\BloodDonationRequest;

use App\Enums\BloodGroupEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateBloodDonationRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        if (request()->method() == 'POST') {
            return [
                'full_name'        => ['required', 'string', 'min:3', 'max:255'],
                'contact_phone'    => ['required', 'string', 'regex:/^07\d{9}$/'],
                'address'          => ['required', 'string', 'min:3', 'max:255'],
                'city_id'          => ['required', 'numeric', 'exists:cities,id'],
                'blood_group'      => ['required', 'string', Rule::in(BloodGroupEnum::getAllValues())],
                'nearest_hospital' => ['required', 'string'],
                'can_wait_until'   => ['required', 'date', 'date_format:Y-m-d H:i'],
                'notes'            => ['nullable', 'string'],
            ];
        }

        return [
            'full_name'        => ['nullable', 'string', 'min:3', 'max:255'],
            'contact_phone'    => ['nullable', 'string', 'regex:/^07\d{9}$/'],
            'address'          => ['nullable', 'string', 'min:3' , 'max:255'],
            'city_id'          => ['nullable', 'numeric', 'exists:cities,id'],
            'blood_group'      => ['nullable', 'string', Rule::in(BloodGroupEnum::getAllValues())],
            'nearest_hospital' => ['nullable', 'string'],
            'can_wait_until'   => ['nullable', 'date', 'date_format:Y-m-d H:i'],
            'notes'            => ['nullable', 'string'],
        ];
    }
}
