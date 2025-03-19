<?php

namespace App\Http\Requests\ClinicEmployee;

use App\Enums\GenderEnum;
use App\Models\ClinicEmployee;
use App\Rules\LanguageShape;
use App\Rules\UniquePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateClinicEmployeeRequest extends FormRequest
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
                'clinic_id' => ['required', 'numeric', 'exists:clinics,id'],

                'first_name' => ['required', new LanguageShape(), 'max:60'],
                'middle_name' => ['required', new LanguageShape(), 'max:60'],
                'last_name' => ['required', new LanguageShape(), 'max:60'],
                'full_name' => ['string', 'nullable',],
                'email' => ['required', 'email', 'max:255', 'min:3', 'string', 'unique:users,email',],
                'password' => 'string|min:8|max:20|required|confirmed',
                'birth_date' => 'date_format:Y-m-d|date|nullable',
                'gender' => ['required', 'string', Rule::in(GenderEnum::getAllValues())],
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
                'address' => 'array|nullable',
                'address.name' => ['nullable', 'min:3', new LanguageShape()],
                'address.city_id' => ['nullable', 'numeric', 'exists:cities,id'],
                'phone_numbers' => 'array|required',
                'phone_numbers.*' => ['required', 'string', 'unique:phone_numbers,phone', 'regex:/^07\d{9}$/',],
            ];
        }

        $userId = ClinicEmployee::findOrFail(request()->route('clinic_employee'))?->user?->id;
        return [
            'clinic_id' => ['nullable', 'numeric', 'exists:clinics,id'],
            'first_name' => ['nullable', new LanguageShape(), 'max:60'],
            'middle_name' => ['nullable', new LanguageShape(), 'max:60'],
            'last_name' => ['nullable', new LanguageShape(), 'max:60'],
            'full_name' => ['string', 'nullable',],
            'email' => ['nullable', 'email', 'max:255', 'min:3', 'string', 'unique:users,email,' . $userId,],
            'password' => 'string|min:8|max:20|nullable|confirmed',
            'birth_date' => 'date_format:Y-m-d|date|nullable',
            'gender' => ['nullable', 'string', Rule::in(GenderEnum::getAllValues())],
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
            'address' => 'array|nullable',
            'address.name' => ['nullable', 'min:3', new LanguageShape()],
            'address.city_id' => ['nullable', 'numeric', 'exists:cities,id'],
            'phone_numbers' => 'array|nullable',
            'phone_numbers.*' => ['required', 'string', 'regex:/^07\d{9}$/', new UniquePhoneNumber($userId),],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (auth()->user()?->isClinic()) {
            $this->merge([
                'clinic_id' => auth()->user()?->getClinicId(),
            ]);
        }
    }
}
