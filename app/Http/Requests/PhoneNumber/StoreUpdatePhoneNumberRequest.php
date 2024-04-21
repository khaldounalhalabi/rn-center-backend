<?php

namespace App\Http\Requests\PhoneNumber;

use App\Models\Hospital;
use App\Models\User;
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
        if (request()->method() == 'PUT') {
            return [
                'phone' => 'nullable|string|max:255|min:6|unique:phone_numbers,phone,' . request()->route('phone_number'),
                'label' => 'string|nullable|min:3'
            ];
        }

        return [
            'phone' => 'nullable|string|max:255|min:6|unique:phone_numbers,phone,' . request()->route('phone_number'),
            'label' => 'string|nullable|min:3',
            'phoneable_type' => 'required|string' . Rule::in([User::class, Hospital::class]),
            'phoneable_id' => 'required|numeric',
        ];
    }
}
