<?php

namespace App\Http\Requests\AuthRequests;

use App\Enums\GenderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AuthRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        //customer register
        return [
            'first_name' => 'required|string|max:255|min:3',
            'middle_name' => 'required|string|max:255|min:3',
            'last_name' => 'required|string|max:255|min:3',
            'mother_full_name' => 'required|string|min:3|max:255',
            'phone_number' => 'array|required',
            'phone_number.*' => 'required|string|min:10|max:11|unique:phone_numbers,phone|phone:IQ',
            'email' => 'required|email|unique:users,email|min:3|max:255',
            'password' => 'required|min:8|confirmed|max:255',
            'fcm_token' => 'nullable|string|min:3|max:1000',
            'gender' => 'required|string|' . Rule::in(GenderEnum::getAllValues()),
            'city' => 'string|max:255|min:3|required',
            'image' => 'image|max:50000|mimes:jpg,png|nullable'
        ];
    }
}
