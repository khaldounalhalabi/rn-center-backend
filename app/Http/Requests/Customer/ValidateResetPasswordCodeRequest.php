<?php

namespace App\Http\Requests\Customer;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ValidateResetPasswordCodeRequest extends FormRequest
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
        return [
            'verification_code' => 'required|string|exists:phone_numbers,verification_code',
        ];
    }
}
