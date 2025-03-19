<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateSettingRequest extends FormRequest
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
        return [
            'value' => ['nullable', 'required_without:image', 'string', 'min:1', 'max:5000', 'exclude_with:image'],
            'image' => 'nullable|required_without:value|image|max:50000|exclude_with:value',
        ];
    }
}
