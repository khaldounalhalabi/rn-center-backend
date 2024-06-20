<?php

namespace App\Http\Requests\AvailableDepartment;

use App\Rules\LanguageShape;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreUpdateAvailableDepartmentRequest extends FormRequest
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
                'name'        => ['required', 'json', new LanguageShape()],
                'description' => ['nullable', 'json', new LanguageShape()],
            ];
        }

        return [
            'name'        => ['nullable', 'json', new LanguageShape()],
            'description' => ['nullable', 'json', new LanguageShape()],
        ];
    }
}
