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
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        if (request()->method() == 'POST') {
            return [
                'clinic_id'            => ['required', 'numeric', 'exists:clinics,id'],
                'customer_id'          => ['required', 'numeric', 'exists:customers,id'],
                'appointment_id'       => ['nullable', 'required_without:customer_id', 'numeric', 'exists:appointments,id'],
                'physical_information' => ['nullable', 'json'],
                'problem_description'  => ['nullable', 'string'],
                'test'                 => ['nullable', 'string'],
                'next_visit'           => ['nullable', 'string', 'min:3', 'max:255'],

                'medicines'                 => 'array|required',
                'medicines.*.medicine_id'   => 'required|exists:medicines,id|numeric',
                'medicines.*.dosage'        => 'string|nullable',
                'medicines.*.duration'      => 'string|nullable',
                'medicines.*.time'          => 'string|nullable',
                'medicines.*.dose_interval' => 'string|nullable',
                'medicines.*.comment'       => 'string|nullable'
            ];
        }

        return [
            'physical_information' => ['nullable', 'json'],
            'problem_description'  => ['nullable', 'string'],
            'test'                 => ['nullable', 'string'],
            'next_visit'           => ['nullable', 'string', 'min:3', 'max:255'],

            'medicines'                 => 'array|nullable',
            'medicines.*.medicine_id'   => 'required|exists:medicines,id|numeric',
            'medicines.*.dosage'        => 'string|nullable',
            'medicines.*.duration'      => 'string|nullable',
            'medicines.*.time'          => 'string|nullable',
            'medicines.*.dose_interval' => 'string|nullable',
            'medicines.*.comment'       => 'string|nullable'
        ];
    }

    public function attributes()
    {
        return [
            'medicines.*.medicine_id'   => 'medicine',
            'medicines.*.dosage'        => 'medicine dosage',
            'medicines.*.duration'      => 'medicine duration',
            'medicines.*.time'          => 'medicine time',
            'medicines.*.dose_interval' => 'medicine dose interval',
            'medicines.*.comment'       => 'comment',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (auth()->user()?->isDoctor()) {
            $this->merge([
                'clinic_id' => auth()->user()?->getClinicId()
            ]);
        }
    }
}
