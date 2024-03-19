<?php

namespace App\Http\Requests\Schedule;

use App\Enums\WeekDayEnum;
use App\Rules\UniqueSchedule;
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
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        $scheduleData = [
            'hospital_id' => $this->hospital_id ?? null,
            'clinic_id' => $this->clinic_id ?? null,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'day_of_week' => $this->day_of_week,
            'schedule_id' => request()->route('schedule') ?? null
        ];

        if (request()->method() == 'POST') {
            return [
                'clinic_id' => 'nullable|numeric|exists:clinics,id|required_without:hospital_id',
                'day_of_week' => 'required|string|' . Rule::in(WeekDayEnum::getAllValues()),
                'start_time' => 'required|date_format:H:i',
                'end_time' => ['required', 'date_format:H:i', 'after:start_time', new UniqueSchedule($scheduleData)],
                'hospital_id' => 'nullable|numeric|exists:hospitals,id|required_without:clinic_id',
            ];
        }
        return [
            'day_of_week' => 'nullable|string',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => ['nullable', 'date_format:H:i', new UniqueSchedule($scheduleData)],
        ];
    }
}
