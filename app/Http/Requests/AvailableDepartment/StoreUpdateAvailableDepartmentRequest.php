<?php

namespace App\Http\Requests\AvailableDepartment;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\LanguageShape;


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
     *
     * @return array<string, Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'json', new LanguageShape()] ,
            'description' => ['nullable', 'json', new LanguageShape()] ,
            'hospital_id' => 'required|numeric|exists:hospitals,id',
        ];
    }



    protected function prepareForValidation()
    {
        if (request()->acceptsHtml()) {
            $this->merge([
                'name' => json_encode($this->name),
                'description' => json_encode($this->description),
            ]);
        }
    }
}
