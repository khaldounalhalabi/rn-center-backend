<?php

namespace App\Http\Requests\v1\Payslip;

use App\Enums\PayslipAdjustmentTypeEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkAdjustmentRequest extends FormRequest
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
            'payrun_id' => 'numeric|' . Rule::exists('payruns', 'id'),
            'formulas' => 'nullable|array|' . Rule::excludeIf(fn() => !is_null($this->input('payslips_ids'))),
            'formulas.*' => 'numeric|exists:formulas,id',
            'payslips_ids' => 'nullable|array|' . Rule::excludeIf(fn() => !is_null($this->input('formulas'))),
            'payslips_ids.*' => 'numeric|exists:payslips,id',
            'amount' => ['required', 'numeric' , 'max:900000000'],
            'reason' => ['required', 'string' , 'max:255'],
            'type' => ['required', 'string', 'min:3', 'max:255', Rule::in(PayslipAdjustmentTypeEnum::getAllValues())],
        ];
    }
}
