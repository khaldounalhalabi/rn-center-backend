<?php

namespace App\Http\Requests\v1\Customer;

use App\Enums\BloodGroupEnum;
use App\Enums\GenderEnum;
use App\Repositories\CustomerRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateCustomerRequest extends FormRequest
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
        if (isDoctor()) {
            return [
                'blood_group' => 'nullable|string|' . Rule::in(BloodGroupEnum::getAllValues()),
                'health_status' => 'nullable|string|max:5000',
                'notes' => 'nullable|string|max:5000',
                'other_data' => 'nullable|array',
                'other_data.*.key' => 'string|min:1|max:255',
                'other_data.*.value' => 'string|min:1|max:5000',
                'attachments' => 'nullable|array',
                'attachments.*' => 'file|mimes:jpeg,png,jpg,pdf,webp,zip,rar,word,txt,docx|max:25000',
            ];
        }

        return [
            'first_name' => 'required|string|min:3|max:255',
            'last_name' => 'required|string|min:3|max:255',
            'phone' => [
                'required',
                'regex:/^09\d{8}$/',
                Rule::unique('users', 'phone')
                    ->when(
                        $this->isPut(),
                        fn($rule) => $rule->ignore(CustomerRepository::make()->find($this->route('customer'))?->user_id)
                    )
            ],
            'gender' => ['required', 'string', Rule::in(GenderEnum::getAllValues())],
            'birth_date' => 'required|date|date_format:Y-m-d',
            'blood_group' => 'nullable|string|' . Rule::in(BloodGroupEnum::getAllValues()),
            'health_status' => 'nullable|string|max:5000',
            'notes' => 'nullable|string|max:5000',
            'other_data' => 'nullable|array',
            'other_data.*.key' => 'string|min:1|max:255',
            'other_data.*.value' => 'string|min:1|max:5000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,pdf,webp,zip,rar,word,txt,docx|max:25000',
        ];
    }
}
