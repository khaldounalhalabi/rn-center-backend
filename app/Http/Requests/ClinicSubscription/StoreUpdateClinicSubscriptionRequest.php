<?php

namespace App\Http\Requests\ClinicSubscription;

use App\Enums\SubscriptionTypeEnum;
use App\Models\ClinicSubscription;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateClinicSubscriptionRequest extends FormRequest
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
        if (request()->method() == "POST") {
            return [
                'clinic_id' => 'required|numeric|exists:clinics,id',
                'subscription_id' => 'required|numeric|exists:subscriptions,id',
                'type' => 'string|required|' . Rule::in(SubscriptionTypeEnum::getAllValues()),
                'deduction_cost' => 'numeric|min:0|nullable|' . Rule::requiredIf($this->input('type') == SubscriptionTypeEnum::BOOKING_COST_BASED->value)
            ];
        }

        return [
            'subscription_id' => 'nullable|numeric|exists:subscriptions,id',
            'type' => 'string|nullable|' . Rule::in(SubscriptionTypeEnum::getAllValues()),
            'deduction_cost' => 'numeric|min:0|nullable|' . Rule::requiredIf($this->input('type') == SubscriptionTypeEnum::BOOKING_COST_BASED->value)
        ];
    }
}
