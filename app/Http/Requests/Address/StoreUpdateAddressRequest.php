<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreUpdateAddressRequest extends FormRequest
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
        if (request()->method() == 'POST') {
            return [
                'name' => 'required|string|min:3|max:255',
                'city' => 'required|string|min:3|max:255',
                'lat' => 'nullable|string',
                'lng' => 'nullable|string',
                'country' => 'required|string|min:3|max:255',
                'addressable_id' => 'required|numeric',
                'addressable_type' => 'required|string|min:3|max:255',
            ];
        }

        return [
            'name' => 'nullable|string|min:3|max:255',
            'city' => 'nullable|string|min:3|max:255',
            'lat' => 'nullable|string',
            'lng' => 'nullable|string',
            'country' => 'nullable|string|min:3|max:255',
            'addressable_id' => 'nullable|numeric',
            'addressable_type' => 'nullable|string|min:3|max:255',
        ];
    }
}
