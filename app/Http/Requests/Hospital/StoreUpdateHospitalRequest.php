<?php

namespace App\Http\Requests\Hospital;

use App\Models\Hospital;
use App\Rules\LanguageShape;
use App\Rules\UniquePhoneNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Propaganistas\LaravelPhone\Rules\Phone;


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
        if (request()->method() == 'POST') {
            return [
                'name' => ['required', 'json', new LanguageShape()],
                'phone_numbers' => 'array|required',
                'phone_numbers.*' => ['required', 'string', 'unique:phone_numbers,phone', (new Phone())->country(['IQ'])],
                'available_departments' => 'array|nullable',
                'available_departments.*' => ['required', 'numeric', 'exists:available_departments,id'],
                "images" => 'array|nullable',
                'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ];
        }

        return [
            'name' => ['nullable', 'json', new LanguageShape()],
            'phone_numbers' => 'array|nullable',
            'phone_numbers.*' => ['nullable', 'string', new UniquePhoneNumber(request()->route('hospital'), Hospital::class), (new Phone())->country(['IQ'])],
            'available_departments' => 'array|nullable',
            'available_departments.*' => ['nullable', 'numeric', 'exists:available_departments,id'],
            "images" => 'array|nullable',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
