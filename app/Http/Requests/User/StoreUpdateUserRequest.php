<?php

namespace App\Http\Requests\User;

use App\Enums\BloodGroupEnum;
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
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|min:3|max:255',
            'middle_name' => 'required|string|min:3|max:255',
            'last_name' => 'required|string|min:3|max:255',
            'phone_number' => 'array|required',
            'phone_number.*' => 'required|string|unique:phone_numbers,phone|phone:IQ',
            'email' => 'unique:users,email|required|string|max:255|email',
            'password' => 'required|string|max:255|min:6|confirmed',
            'birth_date' => 'required|date_format:Y-m-d',
            'gender' => 'required|string',
            'blood_group' => 'required|string|' . Rule::in(BloodGroupEnum::getAllValues()),
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }


}
