<?php

namespace App\Http\Requests\v1\Payrun;

use App\Services\v1\Payrun\PayrunService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePayrunRequest extends FormRequest
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
            'from' => 'required|date|date_format:Y-m-d',
            'to' => 'required|date|date_format:Y-m-d',
            'force_create' => 'boolean|required'
        ];
    }

    protected function prepareForValidation(): void
    {
        if (empty($this->from) || empty($this->to)) {
            $this->offsetUnset('from');
            $this->offsetUnset('to');
        }
    }
}
