<?php

namespace App\Http\Requests\Speciality;

use App\Rules\LanguageShape;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreUpdateSpecialityRequest extends FormRequest
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
            'name' => ['unique:specialities,name', 'required', 'string', 'min:3', 'max:255'],
            'description' => '|nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5000'
        ];
    }
}
