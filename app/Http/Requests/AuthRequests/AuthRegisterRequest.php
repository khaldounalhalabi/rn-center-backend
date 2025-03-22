<?php

namespace App\Http\Requests\AuthRequests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

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
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'unique:phone_numbers,phone', 'regex:/^07\d{9}$/',],
            'password' => 'required|min:8|confirmed|max:255',
            'fcm_token' => 'nullable|string|min:3|max:1000',
            'image' => 'image|max:50000|mimes:jpg,png|nullable',
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
}
