<?php

namespace App\Http\Requests\AuthRequests;

use App\Enums\GenderEnum;
use App\Models\User;
use App\Rules\LanguageShape;
use App\Rules\NotInBlocked;
use App\Rules\UniquePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
    {
        $user = auth()->user();
        return [
            'first_name'         => ['nullable', 'string', 'max:255', 'min:3', new LanguageShape()],
            'middle_name'        => ['nullable', 'string', 'max:255', 'min:3', new LanguageShape()],
            'last_name'          => ['nullable', 'string', 'max:255', 'min:3', new LanguageShape()],
            'full_name'          => ['nullable', 'string', new NotInBlocked()],
            'phone_numbers'      => ['array', 'nullable', Rule::excludeIf(fn() => $user?->isClinic())],
            'phone_numbers.*'    => ['nullable', 'string', 'regex:/^07\d{9}$/', new UniquePhoneNumber($user?->id), new NotInBlocked(), Rule::excludeIf(fn() => $user?->isClinic())],
            'email'              => ['nullable', 'email', 'unique:users,email,' . $user?->id, 'min:3', 'max:255', new NotInBlocked()],
            'password'           => 'nullable|min:8|confirmed|max:255',
            'fcm_token'          => 'nullable|string|min:3|max:1000',
            'gender'             => 'nullable|string|' . Rule::in(GenderEnum::getAllValues()),
            'image'              => 'nullable|image|max:50000|mimes:jpg,png',
            'birth_date'         => 'nullable|date|date_format:Y-m-d',
            'address'            => ['nullable', 'array', Rule::excludeIf(fn() => $user?->isClinic())],
            'address.name'       => ['nullable', 'string', new LanguageShape(), Rule::excludeIf(fn() => $user?->isClinic())],
            'address.city_id'    => ['nullable', 'exists:cities,id', 'integer', Rule::excludeIf(fn() => $user?->isClinic())],
            'address.lat'        => ['nullable', 'string', 'nullable_without:address.map_iframe', Rule::excludeIf(fn() => $user?->isClinic())],
            'address.lng'        => ['nullable', 'string', 'nullable_without:address.map_iframe', Rule::excludeIf(fn() => $user?->isClinic())],
            'address.map_iframe' => ['nullable', 'string', Rule::excludeIf(fn() => $user?->isClinic())]
        ];
    }

    public function attributes(): array
    {
        return [
            'phone_numbers.*' => 'phone number'
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('last_name') && $this->input('first_name') && $this->input('middle_name')) {
            $this->merge([
                'full_name' => User::geuUserFullName($this->input('first_name'), $this->input('middle_name'), $this->input('last_name'))
            ]);
        }
    }
}
