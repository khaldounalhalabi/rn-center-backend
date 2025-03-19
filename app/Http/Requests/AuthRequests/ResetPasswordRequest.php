<?php

namespace App\Http\Requests\AuthRequests;

use App\Rules\ValidResetPasswordCode;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
    public function rules()
    {
        return [
            'reset_password_code' => ['required', 'string', 'exists:users,reset_password_code', 'max:8', new ValidResetPasswordCode()],
            'password' => 'required|string|min:8|confirmed|max:255',
        ];
    }
}
