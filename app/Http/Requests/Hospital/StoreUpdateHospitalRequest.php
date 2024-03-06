<?php

namespace App\Http\Requests\Hospital;

use App\Rules\LanguageShape;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreUpdateHospitalRequest extends FormRequest
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
            'name' => ['required', 'json', new LanguageShape()],
            'phone_numbers' => 'array|nullable',
            'phone_numbers.*' => 'required|string|phone:IQ|unique:phone_numbers,phone',
            'available_departments' => 'array|nullable',
            'available_departments.*' => ['required', 'string', 'unique:available_departments,name', new LanguageShape()],
            "images" => 'array|nullable',
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
