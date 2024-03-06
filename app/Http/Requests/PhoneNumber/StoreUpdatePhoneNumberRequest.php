<?php

namespace App\Http\Requests\PhoneNumber;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdatePhoneNumberRequest extends FormRequest
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
            'phone' => 'unique:phone_numbers,phone|required|string|max:255|min:6',
            'user_id' => 'nullable|numeric|exists:users,id',
            'hospital_id' => 'nullable|numeric|exists:hospitals,id',
        ];
    }
}
