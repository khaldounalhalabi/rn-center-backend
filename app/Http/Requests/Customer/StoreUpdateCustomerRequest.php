<?php

namespace App\Http\Requests\Customer;

use App\Enums\BloodGroupEnum;
use App\Enums\GenderEnum;
use App\Models\Customer;
use App\Models\User;
use App\Rules\LanguageShape;
use App\Rules\NotInBlocked;
use App\Rules\UniquePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateCustomerRequest extends FormRequest
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
        if ($this->method() == "POST") {
            return [
                'first_name'  => ['json', 'required', new LanguageShape(), 'min:3', 'max:60'],
                'middle_name' => ['json', 'required', new LanguageShape(), 'min:3', 'max:60'],
                'last_name'   => ['json', 'required', new LanguageShape(), 'min:3', 'max:60'],
                'full_name'   => ['string', 'nullable', new NotInBlocked()],
                'email'       => ['required', 'email', 'max:255', 'min:3', 'string', 'unique:users,email', new NotInBlocked()],
                'password'    => 'string|min:8|max:20|required|confirmed',
                'birth_date'  => 'date_format:Y-m-d|date|before:20 years ago|required',
                'gender'      => ['required', 'string', Rule::in(GenderEnum::getAllValues())],
                'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
                'tags'        => ['nullable', 'string'],
                'blood_group' => 'nullable|string|' . Rule::in(BloodGroupEnum::getAllValues()),

                'address'            => 'array|required',
                'address.name'       => ['required', 'json', 'min:3', new LanguageShape()],
                'address.city_id'    => ['required', 'numeric', 'exists:cities,id'],
                'address.map_iframe' => ['nullable', 'string'],

                'phone_numbers'   => 'array|required',
                'phone_numbers.*' => ['required', 'string', 'unique:phone_numbers,phone', 'regex:/^07\d{9}$/', new NotInBlocked()],
            ];
        }

        $userId = Customer::find(request()->route('customer'))?->user_id;

        return [
            'first_name'  => ['json', 'nullable', new LanguageShape(), 'min:3', 'max:60'],
            'middle_name' => ['json', 'nullable', new LanguageShape(), 'min:3', 'max:60'],
            'last_name'   => ['json', 'nullable', new LanguageShape(), 'min:3', 'max:60'],
            'full_name'   => ['string', 'nullable', new NotInBlocked()],
            'email'       => ['nullable', 'email', 'max:255', 'min:3', 'string', 'unique:users,email,' . $userId, new NotInBlocked()],
            'password'    => 'string|min:8|max:20|nullable|confirmed',
            'birth_date'  => 'date_format:Y-m-d|date|before:20 years ago|nullable',
            'gender'      => ['nullable', 'string', Rule::in(GenderEnum::getAllValues())],
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
            'tags'        => ['nullable', 'string'],
            'blood_group' => 'nullable|string|' . Rule::in(BloodGroupEnum::getAllValues()),

            'address'            => 'array|nullable',
            'address.name'       => ['nullable', 'json', 'min:3', new LanguageShape()],
            'address.city_id'    => ['nullable', 'numeric', 'exists:cities,id'],
            'address.map_iframe' => ['nullable', 'string'],

            'phone_numbers'   => 'array|nullable',
            'phone_numbers.*' => ['required', 'string', 'regex:/^07\d{9}$/', new UniquePhoneNumber($userId), new NotInBlocked()],
        ];
    }

    public function attributes()
    {
        return [
            'phone_numbers.*' => 'phone number'
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

        if ($this->input('last_name') && $this->input('first_name') && $this->input('middle_name')) {
            $this->merge([
                'full_name' => User::geuUserFullName($this->input('first_name'), $this->input('middle_name'), $this->input('last_name'))
            ]);
        }
    }
}
