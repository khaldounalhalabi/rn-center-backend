<?php

namespace App\Http\Requests\AuthRequests;

use App\Rules\NotInBlocked;
use Illuminate\Foundation\Http\FormRequest;

class AuthLoginRequest extends FormRequest
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
            'email'     => ['required', 'email', 'exists:users,email', 'max:255', new NotInBlocked()],
            'password'  => 'required|min:8|max:255',
            'fcm_token' => 'nullable|string|max:1000',
        ];
    }
}
