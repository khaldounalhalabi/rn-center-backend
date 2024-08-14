<?php

namespace App\Http\Requests\AuthRequests;

use App\Enums\GenderEnum;
use App\Models\User;
use App\Rules\LanguageShape;
use App\Rules\NotInBlocked;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthRegisterRequest extends FormRequest
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
        //customer register
        return [
            'first_name'      => ['required', 'string', 'max:255', 'min:3'],
            'middle_name'     => ['required', 'string', 'max:255', 'min:3'],
            'last_name'       => ['required', 'string', 'max:255', 'min:3'],
            'full_name'       => ['nullable', 'string', new NotInBlocked()],
            'phone_number'    => ['array', 'required'],
            'phone_number.*'  => ['required', 'string', 'unique:phone_numbers,phone', 'regex:/^07\d{9}$/', new NotInBlocked()],
            'password'        => 'required|min:8|confirmed|max:255',
            'fcm_token'       => 'nullable|string|min:3|max:1000',
            'gender'          => 'required|string|' . Rule::in(GenderEnum::getAllValues()),
            'image'           => 'image|max:50000|mimes:jpg,png|nullable',
            'address'         => 'required|array',
            'address.name'    => ['required', 'string', new LanguageShape()],
            'address.city_id' => ['required', 'exists:cities,id', 'integer'],
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
        if ($this->input('address')) {
            $this->merge([
                'address' => [
                    ...$this->input('address'),
                    'map_iframe' => strip_tags($this->input('address.map_iframe'), ['iframe']),
                ],
            ]);
        }
    }

    protected function passedValidation(): void
    {
        $firstName = $this->isArabic($this->input('first_name'))
            ? json_encode(['ar' => $this->input('first_name'), "en" => ""])
            : json_encode(['en' => $this->input('first_name'), "ar" => ""]);

        $middleName = $this->isArabic($this->input('middle_name'))
            ? json_encode(['ar' => $this->input('middle_name'), "en" => ""])
            : json_encode(['en' => $this->input('middle_name'), "ar" => ""]);

        $lastName = $this->isArabic($this->input('last_name'))
            ? json_encode(['ar' => $this->input('last_name'), "en" => ""])
            : json_encode(['en' => $this->input('last_name'), "ar" => ""]);

        $this->replace([
            'first_name'  => $firstName,
            'middle_name' => $middleName,
            'last_name'   => $lastName,
            'full_name'   => User::getUserFullName($firstName, $middleName, $lastName),
        ]);
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
