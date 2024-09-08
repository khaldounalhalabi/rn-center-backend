<?php

namespace App\Http\Requests\Customer;

use App\Enums\BloodGroupEnum;
use App\Enums\GenderEnum;
use App\Models\User;
use App\Rules\LanguageShape;
use App\Rules\NotInBlocked;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DoctorStoreUpdateCustomerRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        if (request()->method() == "POST") {
            return [
                'first_name'      => ['json', 'required', new LanguageShape(), 'max:60'],
                'middle_name'     => ['json', 'required', new LanguageShape(), 'max:60'],
                'last_name'       => ['json', 'required', new LanguageShape(), 'max:60'],
                'full_name'       => ['string', 'nullable', new NotInBlocked()],
                'email'           => ['nullable', 'email', 'max:255', 'min:3', 'string', 'unique:users,email', new NotInBlocked()],
                'birth_date'      => 'nullable|date_format:Y-m-d|date',
                'gender'          => ['required', 'string', Rule::in(GenderEnum::getAllValues())],
                'blood_group'     => ['nullable', 'string', Rule::in(BloodGroupEnum::getAllValues())],
                'address'         => 'array|nullable',
                'address.name'    => ['nullable', 'required_with:address', 'json', 'min:3', new LanguageShape()],
                'address.city_id' => ['nullable', 'required_with:address', 'numeric', 'exists:cities,id'],
                'phone_numbers'   => 'array|required|max:2|min:1',
                'phone_numbers.*' => ['required', 'string', 'regex:/^07\d{9}$/', new NotInBlocked()],

                'medical_condition' => ['required', 'string'],
                'note'              => ['nullable', 'string'],
                'other_data'        => ['nullable', 'json'],
                'images'            => ['nullable', 'array', 'max:20'],
                'images.*'          => ['nullable', 'image', 'max:50000'],
            ];
        }
        return [
            'medical_condition' => ['nullable', 'string'],
            'note'              => ['nullable', 'string'],
            'other_data'        => ['nullable', 'json'],
            'images'            => ['nullable', 'array', 'max:20'],
            'images.*'          => ['nullable', 'image', 'max:50000'],
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

    public function attributes(): array
    {
        return [
            'phone_numbers.*' => 'phone number',
            'images.*'        => 'images',
        ];
    }
}
