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
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'nullable|string|max:255|min:3',
            'middle_name' => 'nullable|string|max:255|min:3',
            'last_name' => 'nullable|string|max:255|min:3',
            'full_name' => ['nullable', 'string', new NotInBlocked()],
            'phone_number' => 'array|nullable',
            'phone_number.*' => ['nullable', 'string', 'unique:phone_numbers,phone', 'regex:/^07\d{9}$/', new UniquePhoneNumber(auth()->user()->id), new NotInBlocked()],
            'email' => ['nullable', 'email', 'unique:users,email', 'min:3', 'max:255', new NotInBlocked()],
            'password' => 'nullable|min:8|confirmed|max:255',
            'fcm_token' => 'nullable|string|min:3|max:1000',
            'gender' => 'nullable|string|' . Rule::in(GenderEnum::getAllValues()),
            'image' => 'image|max:50000|mimes:jpg,png|nullable',
            'birth_date' => 'nullable|date|date_format:Y-m-d',
            'address' => 'nullable|array',
            'address.name' => ['nullable', 'string', new LanguageShape()],
            'address.city_id' => ['nullable', 'exists:cities,id', 'integer'],
            'address.lat' => ['nullable', 'string', 'nullable_without:address.map_iframe'],
            'address.lng' => ['nullable', 'string', 'nullable_without:address.map_iframe'],
            'address.map_iframe' => ['nullable', 'string', 'nullable_without:address.lat', 'nullable_without:address.lng']
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
