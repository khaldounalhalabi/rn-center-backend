<?php

namespace App\Http\Requests\AppointmentDeduction;

use App\Enums\AppointmentDeductionStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkToggleStatusRequest extends FormRequest
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
            'status' => ['required', 'string', Rule::in(AppointmentDeductionStatusEnum::getAllValues())],
            'ids' => 'required|array',
            'ids.*' => ['required', 'int', 'exists:appointment_deductions,id'],
        ];
    }
}
