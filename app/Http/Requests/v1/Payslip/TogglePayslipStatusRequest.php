<?php

namespace App\Http\Requests\v1\Payslip;

use App\Enums\PayslipStatusEnum;
use App\Enums\PermissionEnum;
use App\Models\Payslip;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TogglePayslipStatusRequest extends FormRequest
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
        $payslip = Payslip::find($this->route('payslipId'));
        if ((isAdmin() || can(PermissionEnum::PAYROLL_MANAGEMENT)) && $payslip->user_id != user()->id) {
            $allowed = [
                PayslipStatusEnum::DRAFT->value,
                PayslipStatusEnum::EXCLUDED->value,
            ];
        } else {
            $allowed = [
                PayslipStatusEnum::DRAFT->value,
                PayslipStatusEnum::ACCEPTED->value,
                PayslipStatusEnum::REJECTED->value
            ];
        }

        return [
            'status' => ['required', 'string', Rule::in($allowed)],
        ];
    }
}
