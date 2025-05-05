<?php

namespace App\Http\Requests\v1\Formula;

use App\Rules\ValidFormula;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateFormulaRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'formula' => ['required', 'string', 'min:1', 'max:5000', new ValidFormula],
            'segments' => ['array', 'nullable'],
            'segments.*.name' => ['string', 'min:1', 'max:255', 'nullable'],
            'segments.*.segment' => ['string', 'min:1', 'max:255', 'required_with:segments.*.name', new ValidFormula],
        ];
    }
}
