<?php

namespace App\Http\Requests\Follower;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateFollowerRequest extends FormRequest
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
            'clinic_id'   => ['required', 'numeric', 'exists:clinics,id'],
            'customer_id' => ['required', 'numeric', 'exists:customers,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (auth()->user()?->isCustomer()) {
            $this->merge([
                'customer_id' => auth()->user()?->customer?->id,
            ]);
        }
    }
}
