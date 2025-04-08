<?php

namespace App\Http\Requests\Clinic;

use App\Enums\ClinicStatusEnum;
use App\Enums\GenderEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Models\Clinic;
use App\Models\User;
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
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        $authUser = auth()->user();
        $userId = $authUser->isAdmin()
            ? Clinic::find(request()->route('clinic'))?->user_id
            : $authUser->id;

        if (request()->method() == "POST") {
            return [
                'name' => ['required', 'min:3', 'max:255', new LanguageShape()],
                'appointment_cost' => 'required|numeric|min:0',
                'max_appointments' => 'required|numeric|integer|min:2',
                'status' => 'required|string|' . Rule::in(ClinicStatusEnum::getAllValues()),
                'approximate_appointment_time' => 'numeric|required|max:420|integer|min:5',

                'user' => 'array|required',
                'user.first_name' => ['required', new LanguageShape(), 'max:60'],
                'user.last_name' => ['required', new LanguageShape(), 'max:60'],
                'user.email' => ['required', 'email', 'max:255', 'min:3', 'string', 'unique:users,email',],
                'user.password' => 'string|min:8|max:20|required|confirmed',
                'user.birth_date' => 'date_format:Y-m-d|date|before:20 years ago|nullable',
                'user.gender' => ['required', 'string', Rule::in(GenderEnum::getAllValues())],
                'user.image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',

                'speciality_ids' => 'array|required',
                'speciality_ids.*' => 'required|numeric|exists:specialities,id',

                'subscription_id' => 'required|numeric|exists:subscriptions,id',
                'subscription_type' => 'string|required|' . Rule::in(SubscriptionTypeEnum::getAllValues()),
            ];
        }

        return [
            'name' => ['nullable', 'min:3', 'max:255', new LanguageShape()],
            'appointment_cost' => ['nullable', 'numeric', 'min:0', Rule::excludeIf(fn() => $authUser?->isClinic())],
            'max_appointments' => 'nullable|numeric|min:2',
            'status' => 'nullable|string|' . Rule::in(ClinicStatusEnum::getAllValues()),
            'approximate_appointment_time' => 'numeric|nullable|max:420|integer|min:5',
            'working_start_year' => 'nullable|date|date_format:Y-m-d|before:now',
            'appointment_day_range' => 'nullable|integer|min:1|max:1000',
            'about_us' => 'string|nullable|min:3|max:10000',
            'experience' => 'string|nullable|min:3|max:10000',
            'work_gallery' => ['array', 'nullable', Rule::excludeIf(fn() => auth()->user()?->isAdmin())],
            'work_gallery.*' => 'required_with:work_gallery|image|mimes:jpeg,png,jpg|max:5000',

            'user' => ['array', 'nullable', Rule::excludeIf(fn() => $authUser?->isClinic())],
            'user.first_name' => ['nullable', new LanguageShape(), 'max:60', Rule::excludeIf(fn() => $authUser?->isClinic())],
            'user.last_name' => ['nullable', new LanguageShape(), 'max:60', Rule::excludeIf(fn() => $authUser?->isClinic())],
            'user.email' => ['nullable', 'email', 'max:255', 'min:3', 'string', 'unique:users,email,' . $userId, Rule::excludeIf(fn() => $authUser?->isClinic())],
            'user.password' => ['string', 'min:8', 'max:20', 'nullable', 'confirmed', Rule::excludeIf(fn() => $authUser?->isClinic())],
            'user.birth_date' => ['date_format:Y-m-d', 'date', 'before:20 years ago', 'nullable', Rule::excludeIf(fn() => $authUser?->isClinic())],
            'user.gender' => ['nullable', 'string', Rule::in(GenderEnum::getAllValues()), Rule::excludeIf(fn() => $authUser?->isClinic())],
            'user.image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5000', Rule::excludeIf(fn() => $authUser?->isClinic())],

            'speciality_ids' => 'array|nullable',
            'speciality_ids.*' => 'nullable|numeric|exists:specialities,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'speciality_ids.*' => 'speciality',
        ];
    }

    protected function prepareForValidation(): void
    {

    }
}
