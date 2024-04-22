<?php

namespace App\Http\Requests\Appointment;

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use App\Rules\CanBookAppointment;
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
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        if (request()->method() == 'POST') {
            return [
                'customer_id' => ['required', 'numeric', 'exists:customers,id'],
                'clinic_id' => ['required', 'numeric', 'exists:clinics,id'],
                'note' => ['nullable', 'string'],
                'service_id' => ['required', 'numeric', 'exists:services,id'],
                'extra_fees' => ['nullable', 'numeric'],
                'total_cost' => ['required', 'numeric'],
                'type' => ['required', 'string', 'min:3', 'max:255', Rule::in(AppointmentTypeEnum::getAllValues())],
                'date' => ['required', 'date', 'date_format:Y-m-d'],
                'from' => ['required', 'date_format:H:i'],
                'to' => ['required', 'date_format:H:i'],
                'status' => ['required', 'string', 'min:3', 'max:255', Rule::in(AppointmentStatusEnum::getAllValues())],
                'device_type' => ['nullable', 'string', 'min:3', 'max:255'],
            ];
        }

        return [
            'note' => ['nullable', 'string'],
            'service_id' => ['nullable', 'numeric', 'exists:services,id'],
            'extra_fees' => ['nullable', 'numeric'],
            'total_cost' => ['nullable', 'numeric'],
            'date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'from' => ['nullable', 'date_format:H:i'],
            'to' => ['nullable', 'date_format:H:i'],
            'status' => ['nullable', 'string', 'min:3', 'max:255', Rule::in(AppointmentStatusEnum::getAllValues())],
        ];
    }
}
