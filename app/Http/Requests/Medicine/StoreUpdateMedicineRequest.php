<?php

namespace App\Http\Requests\Medicine;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateMedicineRequest extends FormRequest
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
                'name'        => ['required', 'string', 'min:3', 'max:255',],
                'description' => ['nullable', 'string',],
                'clinic_id'   => ['required', 'numeric', 'exists:clinics,id',],
            ];
        }

        return [
            'name'        => ['nullable', 'string', 'min:3', 'max:255',],
            'description' => ['nullable', 'string',],
            'clinic_id'   => ['nullable', 'numeric', 'exists:clinics,id',],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (auth()->user()?->isDoctor()){
            $this->merge([
                'clinic_id' => auth()->user()?->getClinicId()
            ]);
        }
    }
}
