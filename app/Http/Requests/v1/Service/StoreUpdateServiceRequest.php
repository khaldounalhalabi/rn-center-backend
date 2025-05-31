<?php

namespace App\Http\Requests\v1\Service;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateServiceRequest extends FormRequest
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
        if (isDoctor() && $this->isPut()) {
            return [
                'name' => ['required', 'string', 'max:255', 'min:3'],
                'description' => ['nullable', 'string', 'max:500'],
                'service_category_id' => ['required', 'numeric', 'exists:service_categories,id'],
            ];
        }

        return [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'approximate_duration' => ['required', 'numeric', 'integer', 'min:5'],
            'service_category_id' => ['required', 'numeric', 'exists:service_categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'clinic_id' => ['required', 'numeric', 'exists:clinics,id'],
            'icon' => ['nullable', 'image', 'max:5000']
        ];
    }
}
