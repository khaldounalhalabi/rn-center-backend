<?php

namespace App\Http\Requests\Customer;

use App\Enums\GenderEnum;
use App\Rules\LanguageShape;
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
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        if (request()->method() == "POST") {
            return [
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
                'address.map_iframe' => ['required', 'string'],

                'medical_condition' => 'nullable|string',
            ];
        }
        $userId = request()->route('user');
        return [
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
            'address.map_iframe' => ['nullable', 'string'],

            'medical_condition' => 'nullable|string',
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
    }
}
