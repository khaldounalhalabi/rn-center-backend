<?php

namespace App\Http\Requests\User;

use App\Enums\BloodGroupEnum;
use App\Enums\GenderEnum;
use App\Rules\LanguageShape;
use App\Rules\UniquePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateUserRequest extends FormRequest
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
                'first_name' => ['json', 'required', new LanguageShape(), 'min:3', 'max:60'],
                'middle_name' => ['json', 'required', new LanguageShape(), 'min:3', 'max:60'],
                'last_name' => ['json', 'required', new LanguageShape(), 'min:3', 'max:60'],
                'email' => 'required|email|max:255|min:3|string|unique:users,email',
                'password' => 'string|min:8|max:20|required|confirmed',
                'birth_date' => 'date_format:Y-m-d|date|before:20 years ago|required',
                'gender' => ['required', 'string', Rule::in(GenderEnum::getAllValues())],
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
                'tags' => ['nullable', 'string'],

                'address' => 'array|required',
                'address.name' => ['required', 'json', 'min:3', new LanguageShape()],
                'address.city_id' => ['required', 'numeric', 'exists:cities,id'],
                'address.map_iframe' => ['nullable', 'string'],

                'phone_numbers' => 'array|required',
                'phone_numbers.*' => ['required', 'string', 'unique:phone_numbers,phone', 'regex:/^07\d{9}$/'],
                'role' => 'required|string|exists:roles,name',
            ];
        }

        $userId = request()->route('user');

        return [
            'first_name' => ['json', 'nullable', new LanguageShape(), 'min:3', 'max:60'],
            'middle_name' => ['json', 'nullable', new LanguageShape(), 'min:3', 'max:60'],
            'last_name' => ['json', 'nullable', new LanguageShape(), 'min:3', 'max:60'],
            'email' => 'nullable|email|max:255|min:3|string|unique:users,email,' . $userId,
            'password' => 'string|min:8|max:20|nullable|confirmed',
            'birth_date' => 'date_format:Y-m-d|date|before:20 years ago|nullable',
            'gender' => ['nullable', 'string', Rule::in(GenderEnum::getAllValues())],
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
            'tags' => ['nullable', 'string'],

            'address' => 'array|nullable',
            'address.name' => ['nullable', 'json', 'min:3', new LanguageShape()],
            'address.city_id' => ['nullable', 'numeric', 'exists:cities,id'],
            'address.map_iframe' => ['nullable', 'string'],

            'phone_numbers' => 'array|nullable',
            'phone_numbers.*' => ['required', 'string', 'regex:/^07\d{9}$/', new UniquePhoneNumber($userId)],

            'role' => 'nullable|string|exists:roles,name',
        ];
    }
}
