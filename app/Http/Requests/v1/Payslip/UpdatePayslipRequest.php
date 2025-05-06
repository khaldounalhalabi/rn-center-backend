<?php

namespace App\Http\Requests\v1\Payslip;

use App\Enums\PayslipAdjustmentTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePayslipRequest extends FormRequest
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
            'details.earnings.*.label' => 'required|string|max:255',
            'details.earnings.*.value' => 'required|numeric|min:0|max:900000000',
            'details.deductions.*.label' => 'required|string|max:255',
            'details.deductions.*.value' => 'required|numeric|min:0|max:900000000',
            'payslip_adjustments.*.reason' => 'required|string|max:255',
            'payslip_adjustments.*.value' => 'required|numeric|min:0|max:900000000',
            'payslip_adjustments.*.type' => 'required|string|' . Rule::in(PayslipAdjustmentTypeEnum::getAllValues()),
        ];
    }
}
