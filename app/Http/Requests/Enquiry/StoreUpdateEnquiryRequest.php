<?php

namespace App\Http\Requests\Enquiry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreUpdateEnquiryRequest extends FormRequest
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
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        if (request()->method() == 'POST') {
            return [
                'name' => ['required', 'string', 'min:3', 'max:255'],
                'email' => ['required', 'string', 'max:255', 'email'],
                'message' => ['required', 'string'],
            ];
        }

        return [
            'name' => ['nullable', 'string', 'min:3', 'max:255'],
            'email' => ['nullable', 'string', 'max:255', 'email'],
            'message' => ['nullable', 'string'],
        ];
    }
}
