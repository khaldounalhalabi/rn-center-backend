<?php

namespace App\Http\Requests\Appointment;

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use App\Models\Appointment;
use App\Rules\ClinicOfferBelongToClinic;
use App\Rules\SystemOfferBelongToClinic;
use App\Rules\ValidSystemOffer;
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
        if (request()->method() == 'POST') {
            return [
                'customer_id'         => ['required', 'numeric', 'exists:customers,id'],
                'clinic_id'           => ['required', 'numeric', 'exists:clinics,id'],
                'note'                => ['nullable', 'string'],
                'service_id'          => ['nullable', 'numeric', 'exists:services,id'],
                'extra_fees'          => ['nullable', 'numeric', 'min:0'],
                'discount'            => ['nullable', 'numeric', 'min:0'],
                'type'                => ['required', 'string', 'min:3', 'max:255', Rule::in(AppointmentTypeEnum::getAllValues())],
                'date'                => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
                'status'              => ['required', 'string', 'min:3', 'max:255', Rule::in(AppointmentStatusEnum::getAllValues())],
                'device_type'         => ['nullable', 'string', 'min:3', 'max:255'],
                'cancellation_reason' => 'string|nullable|' . Rule::requiredIf($this->input('status') == AppointmentStatusEnum::CANCELLED->value),
                'system_offers'       => ['array', 'nullable', Rule::excludeIf(fn() => auth()->user()?->isClinic())],
                'system_offers.*'     => [
                    'numeric',
                    'exists:system_offers,id',
                    new ValidSystemOffer($this->input('customer_id')),
                    new SystemOfferBelongToClinic($this->input('clinic_id'))
                ],
                'offers'              => ['array', 'nullable', Rule::excludeIf(fn() => auth()->user()?->isCustomer())],
                'offers.*'            => [
                    'numeric',
                    'exists:offers,id',
                    new ClinicOfferBelongToClinic($this->input('clinic_id'))
                ],
            ];
        }

        $appointment = Appointment::find(request()->route('appointment_id'));

        return [
            'note'                => ['nullable', 'string'],
            'service_id'          => ['nullable', 'numeric', 'exists:services,id'],
            'extra_fees'          => ['nullable', 'numeric', 'min:0'],
            'discount'            => ['nullable', 'numeric', 'min:0'],
            'date'                => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'status'              => ['nullable', 'string', 'min:3', 'max:255', Rule::in(AppointmentStatusEnum::getAllValues())],
            'cancellation_reason' => 'string|nullable|' . Rule::requiredIf($this->input('status') == AppointmentStatusEnum::CANCELLED->value),

            'system_offers'   => ['array', 'nullable', Rule::excludeIf(fn() => auth()->user()?->isClinic())],
            'system_offers.*' => [
                'numeric',
                'exists:system_offers,id',
                new ValidSystemOffer($this->input('customer_id') ?? $appointment->customer_id),
                new SystemOfferBelongToClinic($appointment->clinic_id)
            ],
            'offers'          => ['array', 'nullable', Rule::excludeIf(fn() => auth()->user()?->isCustomer())],
            'offers.*'        => [
                'numeric',
                'exists:offers,id',
                new ClinicOfferBelongToClinic($appointment->clinic_id)
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('extra_fees') == null) {
            $this->merge([
                'extra_fees' => 0
            ]);
        }

        if ($this->input('discount') == null) {
            $this->merge([
                'discount' => 0
            ]);
        }
    }
}
