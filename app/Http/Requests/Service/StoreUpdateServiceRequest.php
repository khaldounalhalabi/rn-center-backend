<?php

namespace App\Http\Requests\Service;

use App\Enums\ServiceStatusEnum;
use App\Rules\LanguageShape;
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
        if (request()->method() == 'POST') {
            return [
                'name' => ['required', 'json', new LanguageShape()],
                'approximate_duration' => ['required', 'numeric', 'integer'],
                'service_category_id' => ['required', 'numeric', 'exists:service_categories,id'],
                'price' => ['required', 'numeric'],
                'status' => ['required', 'string', Rule::in(ServiceStatusEnum::getAllValues())],
                'description' => ['nullable', 'json', new LanguageShape()],
                'clinic_id' => ['required', 'numeric', 'exists:clinics,id'],
            ];
        }

        return [
            'name' => ['nullable', 'json', new LanguageShape()],
            'approximate_duration' => ['nullable', 'numeric', 'integer'],
            'service_category_id' => ['nullable', 'numeric', 'exists:service_categories,id'],
            'price' => ['nullable', 'numeric'],
            'status' => ['nullable', 'string', Rule::in(ServiceStatusEnum::getAllValues())],
            'description' => ['nullable', 'json', new LanguageShape()],
            'clinic_id' => ['nullable', 'numeric', 'exists:clinics,id'],
        ];
    }
}
