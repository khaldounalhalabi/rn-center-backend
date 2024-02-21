<?php

namespace App\Http\Requests\AuthRequests;

use App\Enums\GenderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
        return [
            'first_name' => 'nullable|string|max:255|min:3',
            'middle_name' => 'nullable|string|max:255|min:3',
            'last_name' => 'nullable|string|max:255|min:3',
            'mother_full_name' => 'nullable|string|min:3|max:255',
            'phone_number' => 'nullable|min:10|max:11|unique:users,phone_number,' . auth()->user()->id,
            'email' => 'nullable|email|min:3|max:255|unique:users,email,' . auth()->user()->id,
            'fcm_token' => 'nullable|string|min:3|max:1000',
            'gender' => 'nullable|string|' . Rule::in(GenderEnum::getAllValues()),
            'city' => 'string|max:255|min:3|required',
            'image' => 'image|max:50000|mimes:jpg,png|nullable'
        ];
    }
}
