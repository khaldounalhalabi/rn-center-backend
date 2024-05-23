<?php

namespace App\Http\Requests\ClinicSubscription;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateClinicSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'clinic_id' => 'required|numeric|exists:clinics,id' ,
            'subscription_id' => 'required|numeric|exists:subscriptions,id' ,
            'start_at' => 'nullable|date_format:Y-m-d|'
        ];
    }
}
