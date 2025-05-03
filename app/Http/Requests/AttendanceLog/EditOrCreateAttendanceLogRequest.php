<?php

namespace App\Http\Requests\AttendanceLog;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EditOrCreateAttendanceLogRequest extends FormRequest
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
        $this->requiredAttendTo($this->input('attendance_shifts') ?? []);
        return [
            'attendance_at' => 'required|date_format:Y-m-d',
            'attendance_shifts' => 'nullable|array',
            'attendance_shifts.*.attend_from' => 'required_with:attendance_shifts|date_format:H:i',
            'attendance_shifts.*.attend_to' => [
                'date_format:H:i',
                'after:attendance_shifts.*.attend_from',
                'nullable',
                Rule::requiredIf(fn() => $this->input('attendance_at') != now()->format('Y-m-d'))
            ],
        ];
    }

    private function requiredAttendTo(array $shifts = []): void
    {
        $nullAttendToCount = 0;
        for ($i = 0; $i < count($shifts); $i++) {
            if (!isset($shifts[$i]['attend_to']) && $nullAttendToCount >= 1) {
                throw ValidationException::withMessages([
                    "attendance_shifts.$i.attend_to" => [
                        'message' => 'checkout field is required , because you have another slot without a checkout field value'
                    ]
                ]);
            }
            if (!isset($shifts[$i]['attend_to'])) {
                $nullAttendToCount++;
            }
        }
    }
}
