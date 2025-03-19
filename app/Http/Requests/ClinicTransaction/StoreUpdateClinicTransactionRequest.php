<?php

namespace App\Http\Requests\ClinicTransaction;

use App\Enums\ClinicTransactionStatusEnum;
use App\Enums\ClinicTransactionTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateClinicTransactionRequest extends FormRequest
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
                'amount' => ['required', 'numeric', 'min:0'],
                'type' => ['required', 'string', Rule::in(ClinicTransactionTypeEnum::getAllValues([
                    ClinicTransactionTypeEnum::DEBT_TO_ME->value,
                    ClinicTransactionTypeEnum::SYSTEM_DEBT->value,
                ]))],
                'clinic_id' => ['required', 'numeric', 'exists:clinics,id'],
                'notes' => ['nullable', 'string'],
                'status' => ['nullable', 'string', 'min:3', 'max:255', Rule::in(ClinicTransactionStatusEnum::getAllValues())],
                'date' => ['required', 'date', 'date_format:Y-m-d H:i'],
            ];
        }

        return [
            'amount' => ['nullable', 'numeric'],
            'type' => ['nullable', 'string', 'min:3', 'max:255', Rule::in(ClinicTransactionTypeEnum::getAllValues([
                ClinicTransactionTypeEnum::DEBT_TO_ME->value,
                ClinicTransactionTypeEnum::SYSTEM_DEBT->value,
            ]))],
            'clinic_id' => ['nullable', 'numeric', 'exists:clinics,id'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'min:3', 'max:255', Rule::in(ClinicTransactionStatusEnum::getAllValues())],
            'date' => ['nullable', 'date', 'date_format:Y-m-d H:i'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (auth()->user()?->isClinic()) {
            $this->merge([
                'clinic_id' => auth()->user()?->getClinicId(),
                'status' => ClinicTransactionStatusEnum::DONE->value,
            ]);
        }
    }
}
