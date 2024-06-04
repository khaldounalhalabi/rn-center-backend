<?php

namespace App\Http\Requests\Clinic;

use App\Enums\ClinicStatusEnum;
use App\Enums\GenderEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Models\Clinic;
use App\Models\User;
use App\Rules\LanguageShape;
use App\Rules\NotInBlocked;
use App\Rules\UniquePhoneNumber;
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
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        if (request()->method() == "POST") {
            return [
                'name' => ['required', 'json', 'min:3', 'max:255', new LanguageShape()],
                'appointment_cost' => 'required|numeric|min:0',
                'max_appointments' => 'required|numeric|integer|min:2',
                'phone_numbers' => 'array|required',
                'phone_numbers.*' => ['required', 'string', 'unique:phone_numbers,phone', 'regex:/^07\d{9}$/', new NotInBlocked()],
                'hospital_id' => 'numeric|nullable|exists:hospitals,id',
                'status' => 'required|string|' . Rule::in(ClinicStatusEnum::getAllValues()),
                'approximate_appointment_time' => 'numeric|required|max:420|integer|min:5',

                'user' => 'array|required',
                'user.first_name' => ['json', 'required', new LanguageShape(), 'min:3', 'max:60'],
                'user.middle_name' => ['json', 'required', new LanguageShape(), 'min:3', 'max:60'],
                'user.last_name' => ['json', 'required', new LanguageShape(), 'min:3', 'max:60'],
                'user.full_name' => ['string', 'nullable', new NotInBlocked()],
                'user.email' => ['required', 'email', 'max:255', 'min:3', 'string', 'unique:users,email', new NotInBlocked()],
                'user.password' => 'string|min:8|max:20|required|confirmed',
                'user.birth_date' => 'date_format:Y-m-d|date|before:20 years ago|required',
                'user.gender' => ['required', 'string', Rule::in(GenderEnum::getAllValues())],
                'user.image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',

                'address' => 'array|required',
                'address.name' => ['required', 'json', 'min:3', new LanguageShape()],
                'address.city_id' => ['required', 'numeric', 'exists:cities,id'],
                'address.map_iframe' => ['required', 'string'],

                'speciality_ids' => 'array|required',
                'speciality_ids.*' => 'required|numeric|exists:specialities,id',

                'subscription_id' => 'required|numeric|exists:subscriptions,id',
                'subscription_type' => 'string|required|' . Rule::in(SubscriptionTypeEnum::getAllValues()),
                'subscription_deduction_cost' => 'numeric|min:0|nullable|' . Rule::requiredIf($this->input('type') == SubscriptionTypeEnum::BOOKING_COST_BASED->value)
            ];
        }
        $userId = Clinic::find(request()->route('clinic'))?->user_id;
        return [
            'name' => ['nullable', 'json', 'min:3', 'max:255', new LanguageShape()],
            'appointment_cost' => 'nullable|numeric|min:0',
            'max_appointments' => 'nullable|numeric|min:2',
            'phone_numbers' => 'array|nullable',
            'phone_numbers.*' => ['nullable', 'string', new UniquePhoneNumber($userId), 'regex:/^07\d{9}$/', new NotInBlocked()],
            'hospital_id' => 'numeric|nullable|exists:hospitals,id',
            'status' => 'nullable|string|' . Rule::in(ClinicStatusEnum::getAllValues()),
            'approximate_appointment_time' => 'numeric|nullable|max:420|integer|min:5',

            'user' => 'array|nullable',
            'user.first_name' => ['json', 'nullable', new LanguageShape(), 'min:3', 'max:60'],
            'user.middle_name' => ['json', 'nullable', new LanguageShape(), 'min:3', 'max:60'],
            'user.last_name' => ['json', 'nullable', new LanguageShape(), 'min:3', 'max:60'],
            'user.full_name' => ['nullable', 'string', new NotInBlocked()],
            'user.email' => ['nullable', 'email', 'max:255', 'min:3', 'string', 'unique:users,email,' . $userId, new NotInBlocked()],
            'user.password' => 'string|min:8|max:20|nullable|confirmed',
            'user.birth_date' => 'date_format:Y-m-d|date|before:20 years ago|nullable',
            'user.gender' => ['nullable', 'string', Rule::in(GenderEnum::getAllValues())],
            'user.image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',

            'address' => 'array|nullable',
            'address.name' => ['nullable', 'json', 'min:3', new LanguageShape()],
            'address.city_id' => ['nullable', 'numeric', 'exists:cities,id'],
            'address.map_iframe' => ['nullable', 'string'],


            'speciality_ids' => 'array|nullable',
            'speciality_ids.*' => 'nullable|numeric|exists:specialities,id',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'address' => [
                ...$this->input('address'),
                'map_iframe' => strip_tags($this->input('address.map_iframe'), ['iframe'])
            ]
        ]);

        if ($this->input('user.last_name') && $this->input('user.first_name') && $this->input('user.middle_name')) {
            $this->merge([
                'user' => [
                    ...$this->input('user'),
                    'full_name' => User::geuUserFullName($this->input('user.first_name'), $this->input('user.middle_name'), $this->input('user.last_name'))
                ]
            ]);
        }
    }
}
