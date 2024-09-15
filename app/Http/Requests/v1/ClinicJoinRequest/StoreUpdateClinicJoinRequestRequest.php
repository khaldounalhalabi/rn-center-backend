<?php

namespace App\Http\Requests\v1\ClinicJoinRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateClinicJoinRequestRequest extends FormRequest
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
                'doctor_name'  => ['required', 'string', 'min:3', 'max:255'],
                'clinic_name'  => ['required', 'string', 'min:3', 'max:255'],
                'phone_number' => ['required', 'string', 'max:255', 'min:6'],
                'city_id'      => ['required', 'numeric', 'exists:cities,id'],
            ];
        }

        return [
            'doctor_name'  => ['nullable', 'string', 'min:3', 'max:255'],
            'clinic_name'  => ['nullable', 'string', 'min:3', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:255', 'min:6'],
            'city_id'      => ['nullable', 'numeric', 'exists:cities,id'],
        ];
    }
}
