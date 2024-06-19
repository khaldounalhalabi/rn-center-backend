<?php

namespace App\Http\Requests\ClinicHoliday;

use App\Rules\LanguageShape;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreUpdateClinicHolidayRequest extends FormRequest
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
        if (request()->method() == "POST") {
            return [
                'clinic_id'  => ['required', 'numeric', 'exists:clinics,id'],
                'start_date' => ['required', 'date'],
                'end_date'   => ['required', 'date'],
                'reason'     => ['required', 'json', new LanguageShape()],
            ];
        }

        return [
            'clinic_id'  => ['nullable', 'numeric', 'exists:clinics,id'],
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date'],
            'reason'     => ['nullable', 'json', new LanguageShape()],
        ];
    }

    public function prepareForValidation(): void
    {
        if (auth()->user()?->isDoctor()) {
            $this->merge([
                'clinic_id' => auth()->user()?->getClinicId()
            ]);
        }
    }
}
