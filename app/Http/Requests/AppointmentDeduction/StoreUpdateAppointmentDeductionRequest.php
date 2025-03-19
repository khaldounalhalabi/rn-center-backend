<?php

namespace App\Http\Requests\AppointmentDeduction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateAppointmentDeductionRequest extends FormRequest
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
                'amount' => ['required', 'numeric'],
                'status' => ['required', 'string', 'min:3', 'max:255'],
                'clinic_transaction_id' => ['required', 'numeric', 'exists:clinic_transactions,id'],
                'appointment_id' => ['required', 'numeric', 'exists:appointments,id'],
                'clinic_id' => ['required', 'numeric', 'exists:clinics,id'],
                'date' => ['required', 'date', 'date_format:Y-m-d H:i'],
            ];
        }

        return [
            'amount' => ['nullable', 'numeric'],
            'status' => ['nullable', 'string', 'min:3', 'max:255'],
            'clinic_transaction_id' => ['nullable', 'numeric', 'exists:clinic_transactions,id'],
            'appointment_id' => ['nullable', 'numeric', 'exists:appointments,id'],
            'clinic_id' => ['nullable', 'numeric', 'exists:clinics,id'],
            'date' => ['nullable', 'date'],
        ];
    }
}
