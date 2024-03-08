<?php

namespace App\Http\Requests\Clinic;

use App\Rules\LanguageShape;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreUpdateClinicRequest extends FormRequest
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
        if (request()->method() == "POST") {
            return [
                'name' => ['required', 'string', 'min:3', 'max:255'],
                'appointment_cost' => 'required|numeric',
                'working_start_year' => 'required|date',
                'max_appointments' => 'required|numeric',
                'appointment_day_range' => 'required|numeric',
                'about_us' => ['required', 'json', new LanguageShape()],
                'experience' => ['required', 'json', new LanguageShape()],
                'work_gallery' => 'array|nullable',
                'work_gallery.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'phone_numbers' => 'array|required' ,
                'phone_numbers.*' => 'required|phone:IQ|string',
            ];
        }

        return [
            'name' => ['json', new LanguageShape(), 'nullable'],
            'appointment_cost' => 'nullable|numeric',
            'working_start_year' => 'nullable|date',
            'max_appointments' => 'nullable|numeric',
            'appointment_day_range' => 'nullable|numeric',
            'about_us' => ['json', new LanguageShape(), 'nullable'],
            'experience' => ['json', new LanguageShape(), 'nullable'],
            'work_gallery' => 'array|nullable',
            'work_gallery.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ];
    }
}
