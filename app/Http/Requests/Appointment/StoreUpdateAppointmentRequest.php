<?php

namespace App\Http\Requests\Appointment;

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateAppointmentRequest extends FormRequest
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
                'customer_id' => ['required', 'numeric', 'exists:customers,id'],
                'clinic_id' => ['required', 'numeric', 'exists:clinics,id'],
                'note' => ['nullable', 'string'],
                'service_id' => ['nullable', 'numeric', 'exists:services,id'],
                'extra_fees' => ['nullable', 'numeric' , 'min:0'],
                'type' => ['required', 'string', 'min:3', 'max:255', Rule::in(AppointmentTypeEnum::getAllValues())],
                'date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
                'status' => ['required', 'string', 'min:3', 'max:255', Rule::in(AppointmentStatusEnum::getAllValues())],
                'device_type' => ['nullable', 'string', 'min:3', 'max:255'],
                'cancellation_reason' => 'string|nullable|' . Rule::requiredIf($this->input('status') == AppointmentStatusEnum::CANCELLED->value),
            ];
        }

        return [
            'note' => ['nullable', 'string'],
            'service_id' => ['nullable', 'numeric', 'exists:services,id'],
            'extra_fees' => ['nullable', 'numeric' , 'min:0'],
            'date' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'status' => ['nullable', 'string', 'min:3', 'max:255', Rule::in(AppointmentStatusEnum::getAllValues())],
            'cancellation_reason' => 'string|nullable|' . Rule::requiredIf($this->input('status') == AppointmentStatusEnum::CANCELLED->value),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('extra_fees') == null) {
            $this->merge([
                'extra_fees' => 0
            ]);
        }
    }
}
