<?php

namespace App\Http\Requests\v1\AppointmentLog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateAppointmentLogRequest extends FormRequest
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
                'appointment_id' => ['nullable', 'numeric', 'exists:appointments,id', 'unique:appointment_logs,appointment_id',],
                'cancellation_reason' => ['required', 'string', 'unique:appointment_logs,cancellation_reason',],
                'status' => ['nullable', 'string', 'min:3', 'max:255', 'unique:appointment_logs,status',],
                'actor_id' => ['required', 'numeric', 'exists:actors,id', 'unique:appointment_logs,actor_id',],
                'affected_id' => ['required', 'numeric', 'exists:affecteds,id', 'unique:appointment_logs,affected_id',],
                'happen_in' => ['nullable', 'date', 'unique:appointment_logs,happen_in',],

            ];
        }

        return [
            'appointment_id' => ['nullable', 'numeric', 'exists:appointments,id', 'unique:appointment_logs,appointment_id',],
            'cancellation_reason' => ['nullable', 'string', 'unique:appointment_logs,cancellation_reason',],
            'status' => ['nullable', 'string', 'min:3', 'max:255', 'unique:appointment_logs,status',],
            'actor_id' => ['nullable', 'numeric', 'exists:actors,id', 'unique:appointment_logs,actor_id',],
            'affected_id' => ['nullable', 'numeric', 'exists:affecteds,id', 'unique:appointment_logs,affected_id',],
            'happen_in' => ['nullable', 'date', 'unique:appointment_logs,happen_in',],
        ];
    }


}
