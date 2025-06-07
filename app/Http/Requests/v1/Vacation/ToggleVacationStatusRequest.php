<?php

namespace App\Http\Requests\v1\Vacation;

use App\Enums\VacationStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ToggleVacationStatusRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'vacation_id' => 'required|numeric|exists:vacations,id',
            'status' => ['string', VacationStatusEnum::validationRule(), 'max:255']
        ];
    }
}
