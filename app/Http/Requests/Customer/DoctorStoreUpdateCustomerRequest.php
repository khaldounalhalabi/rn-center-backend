<?php

namespace App\Http\Requests\Customer;

use App\Enums\BloodGroupEnum;
use App\Enums\GenderEnum;
use App\Models\Customer;
use App\Models\User;
use App\Rules\LanguageShape;
use App\Rules\NotInBlocked;
use App\Rules\UniquePhoneNumber;
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
                'first_name'      => ['json', 'required', new LanguageShape(), 'min:3', 'max:60'],
                'middle_name'     => ['json', 'required', new LanguageShape(), 'min:3', 'max:60'],
                'last_name'       => ['json', 'required', new LanguageShape(), 'min:3', 'max:60'],
                'full_name'       => ['string', 'nullable', new NotInBlocked()],
                'email'           => ['nullable', 'email', 'max:255', 'min:3', 'string', 'unique:users,email', new NotInBlocked()],
                'birth_date'      => 'required|date_format:Y-m-d|date',
                'gender'          => ['required', 'string', Rule::in(GenderEnum::getAllValues())],
                'blood_group'     => ['required', 'string', Rule::in(BloodGroupEnum::getAllValues())],
                'address'         => 'array|nullable',
                'address.name'    => ['nullable', 'required_with:address', 'json', 'min:3', new LanguageShape()],
                'address.city_id' => ['nullable', 'required_with:address', 'numeric', 'exists:cities,id'],
                'phone_numbers'   => 'array|nullable',
                'phone_numbers.*' => ['required', 'string', 'regex:/^07\d{9}$/', new NotInBlocked()],

                'medical_condition' => ['required', 'string'],
                'note'              => ['nullable', 'string'],
                'other_data'        => ['nullable', 'json'],
                'images'            => ['nullable', 'array', 'max:20'],
                'images.*'          => ['nullable', 'image', 'max:50000'],
            ];
        }
        $currentCustomer = Customer::where('id', request()->route('customerId'))->with(['user'])->first();

        return [
            'first_name'        => ['json', 'nullable', new LanguageShape(), 'min:3', 'max:60'],
            'middle_name'       => ['json', 'nullable', new LanguageShape(), 'min:3', 'max:60'],
            'last_name'         => ['json', 'nullable', new LanguageShape(), 'min:3', 'max:60'],
            'full_name'         => ['string', 'nullable', new NotInBlocked()],
            'email'             => [
                'nullable',
                'email',
                'max:255',
                'min:3',
                'string',
                'unique:users,email,' . $currentCustomer?->user?->email ?? "",
                new NotInBlocked()
            ],
            'birth_date'        => 'date_format:Y-m-d|date|nullable',
            'gender'            => ['nullable', 'string', Rule::in(GenderEnum::getAllValues())],
            'blood_group'     => ['required', 'string', Rule::in(BloodGroupEnum::getAllValues())],
            'address'           => 'array|nullable',
            'address.name'      => ['nullable', 'required_with:address', 'json', 'min:3', new LanguageShape()],
            'address.city_id'   => ['nullable', 'required_with:address', 'numeric', 'exists:cities,id'],
            'phone_numbers'     => 'array|nullable',
            'phone_numbers.*'   => [
                'required',
                'string',
                new UniquePhoneNumber($currentCustomer?->user?->id),
                'regex:/^07\d{9}$/',
                new NotInBlocked()
            ],
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
                'full_name' => User::geuUserFullName($this->input('first_name'), $this->input('middle_name'), $this->input('last_name'))
            ]);
        }
    }

    public function attributes()
    {
        return [
            'phone_numbers.*' => 'phone number',
            'images.*'        => 'images'
        ];
    }
}
