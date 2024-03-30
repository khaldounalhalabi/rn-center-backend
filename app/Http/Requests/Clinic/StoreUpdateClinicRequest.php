<?php

namespace App\Http\Requests\Clinic;

use App\Enums\ClinicStatusEnum;
use App\Enums\GenderEnum;
use App\Models\Clinic;
use App\Rules\LanguageShape;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;


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
                'name' => ['required', 'json', 'min:3', 'max:255', new LanguageShape()],
                'appointment_cost' => 'required|numeric',
                'max_appointments' => 'required|numeric',
                'phone_numbers' => 'array|required',
                'phone_numbers.*' => ['required', 'string', 'unique:phone_numbers,phone'],
                'hospital_id' => 'numeric|nullable|exists:hospitals,id',
                'status' => 'required|string|' . Rule::in(ClinicStatusEnum::getAllValues()),

                'user' => 'array|required',
                'user.first_name' => ['json', 'required', new LanguageShape(), 'min:3', 'max:60'],
                'user.middle_name' => ['json', 'required', new LanguageShape(), 'min:3', 'max:60'],
                'user.last_name' => ['json', 'required', new LanguageShape(), 'min:3', 'max:60'],
                'user.email' => 'required|email|max:255|min:3|string|unique:users,email',
                'user.password' => 'string|min:8|max:20|required|confirmed',
                'user.birth_date' => 'date_format:Y-m-d|date|before:20 years ago|required',
                'user.gender' => ['required', 'string', Rule::in(GenderEnum::getAllValues())],
                'user.image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',

                'address' => 'array|required',
                'address.name' => ['required', 'json', 'min:3', new LanguageShape()],
                'address.city_id' => ['required', 'numeric', 'exists:cities,id'],
                'address.lat' => 'required|string',
                'address.lng' => 'required|string',

                'speciality_ids' => 'array|required',
                'speciality_ids.*' => 'required|numeric|exists:specialities,id',
            ];
        }
        $userId = Clinic::find(request()->route('clinic'))?->user_id;
        return [
            'name' => ['nullable', 'json', 'min:3', 'max:255', new LanguageShape()],
            'appointment_cost' => 'nullable|numeric',
            'max_appointments' => 'nullable|numeric',
            'phone_numbers' => 'array|nullable',
            'phone_numbers.*' => ['nullable', 'string', 'unique:phone_numbers,phone', (new Phone())->country(['IQ'])],
            'hospital_id' => 'numeric|nullable|exists:hospitals,id',
            'status' => 'nullable|string|' . Rule::in(ClinicStatusEnum::getAllValues()),

            'user' => 'array|nullable',
            'user.first_name' => ['json', 'nullable', new LanguageShape(), 'min:3', 'max:60'],
            'user.middle_name' => ['json', 'nullable', new LanguageShape(), 'min:3', 'max:60'],
            'user.last_name' => ['json', 'nullable', new LanguageShape(), 'min:3', 'max:60'],
            'user.email' => 'nullable|email|max:255|min:3|string|unique:users,email,' . $userId,
            'user.password' => 'string|min:8|max:20|nullable|confirmed',
            'user.birth_date' => 'date_format:Y-m-d|date|before:20 years ago|nullable',
            'user.gender' => ['nullable', 'string', Rule::in(GenderEnum::getAllValues())],
            'user.image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',

            'address' => 'array|nullable',
            'address.name' => ['nullable', 'json', 'min:3', new LanguageShape()],
            'address.city_id' => ['nullable', 'numeric', 'exists:cities,id'],
            'address.lat' => 'nullable|string',
            'address.lng' => 'nullable|string',

            'speciality_ids' => 'array|nullable',
            'speciality_ids.*' => 'nullable|numeric|exists:specialities,id',
        ];
    }
}
