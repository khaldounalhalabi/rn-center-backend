<?php

namespace App\Http\Requests\Appointment;

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateAppointmentRequest extends FormRequest
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
            'customer_id' => ['nullable', Rule::requiredIf($this->isPost()), Rule::excludeIf($this->isPut()), 'numeric', 'exists:customers,id'],
            'clinic_id' => ['nullable', Rule::requiredIf($this->isPost()), Rule::excludeIf($this->isPut()), 'numeric', 'exists:clinics,id'],
            'note' => 'nullable|string|max:10000',
            'service_id' => ['nullable', 'numeric', 'exists:services,id'],
            'extra_fees' => ['nullable', 'numeric', 'min:0'],
            'type' => ['string', Rule::in(AppointmentTypeEnum::getAllValues())],
            'date_time' => ['required', 'date_format:Y-m-d H:i'],
            'status' => ['string', Rule::in(AppointmentStatusEnum::getAllValues())],
            'discount' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('extra_fees') == null) {
            $this->merge([
                'extra_fees' => 0,
            ]);
        }

        if ($this->input('discount') == null) {
            $this->merge([
                'discount' => 0,
            ]);
        }

        if (isDoctor()) {
            $this->merge([
                'clinic_id' => clinic()?->id,
                'type' => AppointmentTypeEnum::MANUAL->value,
            ]);
        }

        if (isAdmin()){
            $this->merge([
                'type' => AppointmentTypeEnum::MANUAL->value,
            ]);
        }

        if (auth()?->user()?->isCustomer()) {
            $this->merge([
                'type' => AppointmentTypeEnum::ONLINE->value,
                'status' => AppointmentStatusEnum::PENDING->value,
                'customer_id' => customer()?->id,
            ]);
        }
    }
}
