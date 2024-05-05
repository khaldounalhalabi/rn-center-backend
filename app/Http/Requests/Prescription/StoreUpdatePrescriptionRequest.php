<?php

namespace App\Http\Requests\Prescription;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdatePrescriptionRequest extends FormRequest
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
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        if (request()->method() == 'POST') {
            return [
                'clinic_id' => ['required', 'numeric', 'exists:clinics,id'],
                'customer_id' => ['required', 'numeric', 'exists:customers,id'],
                'physical_information' => ['nullable', 'json'],
                'problem_description' => ['nullable', 'string'],
                'test' => ['nullable', 'string'],
                'next_visit' => ['nullable', 'string', 'min:3', 'max:255'],

                'medicines' => 'array|required',
                'medicines.*.medicine_id' => 'required|exists:medicines,id|numeric',
                'medicines.*.dosage' => 'string|nullable',
                'medicines.*.duration' => 'string|nullable',
                'medicines.*.time' => 'string|nullable',
                'medicines.*.dose_interval' => 'string|nullable',
                'comment' => 'string|nullable'
            ];
        }

        return [
            'clinic_id' => ['nullable', 'numeric', 'exists:clinics,id'],
            'customer_id' => ['nullable', 'numeric', 'exists:customers,id'],
            'physical_information' => ['nullable', 'json'],
            'problem_description' => ['nullable', 'string'],
            'test' => ['nullable', 'string'],
            'next_visit' => ['nullable', 'string', 'min:3', 'max:255'],

            'medicines' => 'array|nullable',
            'medicines.*.medicine_id' => 'required|exists:medicines,id|numeric',
            'medicines.*.dosage' => 'string|nullable',
            'medicines.*.duration' => 'string|nullable',
            'medicines.*.time' => 'string|nullable',
            'medicines.*.dose_interval' => 'string|nullable',
            'comment' => 'string|nullable'
        ];
    }
}
