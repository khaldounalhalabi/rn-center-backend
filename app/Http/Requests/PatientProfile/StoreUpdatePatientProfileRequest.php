<?php

namespace App\Http\Requests\PatientProfile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdatePatientProfileRequest extends FormRequest
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
                'customer_id'       => ['required', 'numeric', 'exists:customers,id'],
                'clinic_id'         => ['required', 'numeric', 'exists:clinics,id'],
                'medical_condition' => ['required', 'string'],
                'note'              => ['nullable', 'string'],
                'other_data'        => ['nullable', 'json'],
                'images'            => ['nullable', 'array', 'max:20'],
                'images.*'          => ['nullable', 'image', 'max:50000'],
            ];
        }

        return [
            'customer_id'       => ['nullable', 'numeric', 'exists:customers,id',],
            'clinic_id'         => ['nullable', 'numeric', 'exists:clinics,id',],
            'medical_condition' => ['nullable', 'string'],
            'note'              => ['nullable', 'string'],
            'other_data'        => ['nullable', 'json'],
            'images'            => ['nullable', 'array', 'max:20'],
            'images.*'          => ['nullable', 'image', 'max:50000'],
        ];
    }
}
