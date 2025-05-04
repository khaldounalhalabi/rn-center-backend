<?php

namespace App\Http\Requests\Schedule;

use App\Enums\WeekDayEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateScheduleRequest extends FormRequest
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
            'schedules' => 'array|required',
            'schedules.*.day_of_week' => 'required|string|' . Rule::in(WeekDayEnum::getAllValues()),
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => ['required', 'date_format:H:i', 'after:schedules.*.start_time'],
            'clinic_id' => 'numeric|exists:clinics,id|nullable|required_without:user_id',
            'user_id' => ['numeric', 'nullable', 'required_without:clinic_id', 'exists:users,id']
        ];
    }

    public function attributes(): array
    {
        return [
            'schedules.*.day_of_week' => 'schedule week day',
            'schedules.*.start_time' => 'schedule start time',
            'schedules.*.end_time' => 'schedule end time',
        ];
    }
}
