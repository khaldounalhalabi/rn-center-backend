<?php

namespace App\Http\Requests\v1\Payslip;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BulkPdfDownloadRequest extends FormRequest
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
            'custom_filter' => 'nullable|string|required_without:payslip_ids',
            'payslip_ids' => 'nullable|array|min:1|required_without:custom_filter',
            'payslip_ids.*' => 'numeric|required|exists:payslips,id',
            'payrun_id' => 'required|numeric|exists:payruns,id',
        ];
    }
}
