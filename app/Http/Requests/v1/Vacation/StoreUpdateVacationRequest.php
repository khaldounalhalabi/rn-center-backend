<?php

namespace App\Http\Requests\v1\Vacation;

use App\Enums\VacationStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateVacationRequest extends FormRequest
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
            'user_id' => ['required', 'numeric', 'exists:users,id'],
            'from' => ['required', 'date', 'after_or_equal:today', 'date_format:Y-m-d'],
            'to' => ['required', 'date', 'after_or_equal:today', 'date_format:Y-m-d'],
            'reason' => ['required', 'string', 'max:5000'],
            'status' => ['nullable', 'string', 'min:3', 'max:255', VacationStatusEnum::validationRule(), Rule::excludeIf(fn() => !isAdmin())],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (isDoctor()) {
            $this->merge([
                'user_id' => user()->id,
            ]);
        }

        if (isAdmin()) {
            $this->merge([
                'status' => VacationStatusEnum::DRAFT->value,
            ]);
        }
    }
}
