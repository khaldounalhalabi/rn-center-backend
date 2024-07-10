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
            'first_name'      => ['required', 'string', 'max:255', 'min:3', new LanguageShape()],
            'middle_name'     => ['required', 'string', 'max:255', 'min:3', new LanguageShape()],
            'last_name'       => ['required', 'string', 'max:255', 'min:3', new LanguageShape()],
            'full_name'       => ['nullable', 'string', new NotInBlocked()],
            'phone_number'    => ['array', 'required'],
            'phone_number.*'  => ['required', 'string', 'unique:phone_numbers,phone', 'regex:/^07\d{9}$/', new NotInBlocked()],
            'email'           => ['required', 'email', 'unique:users,email', 'min:3', 'max:255', new NotInBlocked()],
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
            'phone_numbers.*' => 'phone number'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'address' => [
                ...$this->input('address'),
                'map_iframe' => strip_tags($this->input('address.map_iframe'), ['iframe'])
            ],
        ]);

        if ($this->input('last_name') && $this->input('first_name') && $this->input('middle_name')) {
            $this->merge([
                'full_name' => User::getUserFullName($this->input('first_name'), $this->input('middle_name'), $this->input('last_name'))
            ]);
        }
    }
}
