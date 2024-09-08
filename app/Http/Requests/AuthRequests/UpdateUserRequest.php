<?php

namespace App\Http\Requests\AuthRequests;

use App\Enums\BloodGroupEnum;
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
            'first_name'         => ['nullable', 'string', 'max:255'],
            'middle_name'        => ['nullable', 'string', 'max:255'],
            'last_name'          => ['nullable', 'string', 'max:255'],
            'full_name'          => ['nullable', 'string', new NotInBlocked()],
            'phone_numbers'      => ['array', 'nullable', Rule::excludeIf(fn () => $user?->isClinic())],
            'phone_numbers.*'    => ['nullable', 'string', 'regex:/^07\d{9}$/', new UniquePhoneNumber($user?->id), new NotInBlocked(), Rule::excludeIf(fn () => $user?->isClinic())],
            'email'              => ['nullable', 'email', 'unique:users,email,' . $user?->id, 'min:3', 'max:255', new NotInBlocked()],
            'password'           => 'nullable|min:8|confirmed|max:255',
            'fcm_token'          => 'nullable|string|min:3|max:1000',
            'gender'             => 'nullable|string|' . Rule::in(GenderEnum::getAllValues()),
            'blood_group'        => 'nullable|string|' . Rule::in(BloodGroupEnum::getAllValues()),
            'image'              => 'nullable|image|max:50000|mimes:jpg,png',
            'birth_date'         => 'nullable|date|date_format:Y-m-d',
            'address'            => ['nullable', 'array', Rule::excludeIf(fn () => $user?->isClinic())],
            'address.name'       => ['nullable', 'string', Rule::excludeIf(fn () => $user?->isClinic())],
            'address.city_id'    => ['nullable', 'exists:cities,id', 'integer', Rule::excludeIf(fn () => $user?->isClinic())],
            'address.lat'        => ['nullable', 'string', Rule::excludeIf(fn () => $user?->isClinic())],
            'address.lng'        => ['nullable', 'string', Rule::excludeIf(fn () => $user?->isClinic())],
            'address.map_iframe' => ['nullable', 'string', Rule::excludeIf(fn () => $user?->isClinic())],
        ];
    }

    public function attributes(): array
    {
        return [
            'phone_numbers.*' => 'phone number',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('last_name') && $this->input('first_name') && $this->input('middle_name')) {
            $this->merge([
                'full_name' => User::getUserFullName($this->input('first_name'), $this->input('middle_name'), $this->input('last_name')),
            ]);
        }
    }

    protected function passedValidation(): void
    {
        if (auth()->user()?->isCustomer()) {
            $firstName = $this->isArabic($this->input('first_name'))
                ? json_encode(['ar' => $this->input('first_name'), "en" => ""])
                : json_encode(['en' => $this->input('first_name'), "ar" => ""]);

            $middleName = $this->isArabic($this->input('middle_name'))
                ? json_encode(['ar' => $this->input('middle_name'), "en" => ""])
                : json_encode(['en' => $this->input('middle_name'), "ar" => ""]);

            $lastName = $this->isArabic($this->input('last_name'))
                ? json_encode(['ar' => $this->input('last_name'), "en" => ""])
                : json_encode(['en' => $this->input('last_name'), "ar" => ""]);

            $address = $this->isArabic($this->input('address.name'))
                ? json_encode(['ar' => $this->input('address.name'), "en" => ""])
                : json_encode(['en' => $this->input('address.name'), "ar" => ""]);

            $this->merge([
                'first_name'  => $firstName,
                'middle_name' => $middleName,
                'last_name'   => $lastName,
                'full_name'   => User::getUserFullName($firstName, $middleName, $lastName),
                'address'     => [
                    ...$this->input('address'),
                    'name' => $address,
                ],
            ]);
        }
    }


    private function isArabic($string): bool
    {
        if (preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{08A0}-\x{08FF}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]/u', $string)) {
            return true;
        } else {
            return false;
        }
    }
}
