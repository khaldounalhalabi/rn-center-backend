<?php

namespace App\Http\Requests\v1\AvailableAppointmentTime;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GetAvailableAppointmentTimesRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'clinic_id' => 'required|numeric|exists:clinics,id',
            'date' => 'required|date|date_format:Y-m-d',
        ];
    }
}
