<?php

namespace App\Http\Requests\v1\MedicalRecord;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateMedicalRecordRequest extends FormRequest
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
        return [
            'customer_id' => [
                'nullable',
                'numeric',
                'exists:customers,id',
                Rule::in(Customer::byClinic($this->input('clinic_id'))->select('id')->pluck('id')->toArray()),
                Rule::requiredIf(fn() => $this->isPost()),
                Rule::excludeIf(fn() => $this->isPut())
            ],
            'clinic_id' => ['required', 'numeric', 'exists:clinics,id'],
            'summary' => ['nullable', 'string'],
            'diagnosis' => ['nullable', 'string'],
            'treatment' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (isDoctor()) {
            $this->merge([
                'clinic_id' => clinic()?->id,
            ]);
        }
    }
}
