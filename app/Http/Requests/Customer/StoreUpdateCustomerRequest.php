<?php

namespace App\Http\Requests\Customer;

use App\Enums\BloodGroupEnum;
use App\Enums\GenderEnum;
use App\Models\Customer;
use App\Models\User;
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
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        if ($this->method() == "POST") {
            return [
                'first_name' => ['required', new LanguageShape(), 'max:60'],
                'last_name' => ['required', new LanguageShape(), 'max:60'],
                'email' => ['required', 'email', 'max:255', 'min:3', 'string', 'unique:users,email',],
                'password' => 'string|min:8|max:20|required|confirmed',
                'birth_date' => 'date_format:Y-m-d|date|nullable',
                'gender' => ['required', 'string', Rule::in(GenderEnum::getAllValues())],
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
                'blood_group' => 'nullable|string|' . Rule::in(BloodGroupEnum::getAllValues()),
            ];
        }

        $userId = Customer::find(request()->route('customer'))?->user_id;

        return [
            'first_name' => ['nullable', new LanguageShape(), 'max:60'],
            'last_name' => ['nullable', new LanguageShape(), 'max:60'],
            'email' => ['nullable', 'email', 'max:255', 'min:3', 'string', 'unique:users,email,' . $userId,],
            'password' => 'string|min:8|max:20|nullable|confirmed',
            'birth_date' => 'date_format:Y-m-d|date|nullable',
            'gender' => ['nullable', 'string', Rule::in(GenderEnum::getAllValues())],
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5000',
            'blood_group' => 'nullable|string|' . Rule::in(BloodGroupEnum::getAllValues()),
        ];
    }

    public function attributes()
    {
        return [
        ];
    }

    protected function prepareForValidation(): void
    {

    }
}
