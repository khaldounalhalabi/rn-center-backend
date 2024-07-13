<?php

namespace App\Http\Requests\Appointment;

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use App\Models\Appointment;
use App\Rules\ClinicOfferBelongToClinic;
use App\Rules\CustomerBelongToClinic;
use App\Rules\SystemOfferBelongToClinic;
use App\Rules\ValidSystemOffer;
use hisorange\BrowserDetect\Parser as Browser;
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
                'customer_id'         => ['required', 'numeric', 'exists:customers,id', new CustomerBelongToClinic($this->input('clinic_id'))],
                'clinic_id'           => ['required', 'numeric', 'exists:clinics,id'],
                'note'                => ['nullable', 'string', Rule::excludeIf(fn() => auth()?->user()?->isCustomer())],
                'service_id'          => ['nullable', 'numeric', 'exists:services,id', Rule::excludeIf(fn() => auth()?->user()?->isCustomer())],
                'extra_fees'          => ['nullable', 'numeric', 'min:0', Rule::excludeIf(fn() => auth()?->user()?->isCustomer())],
                'discount'            => ['nullable', 'numeric', 'min:0', Rule::excludeIf(fn() => auth()?->user()?->isCustomer())],
                'type'                => ['required', 'string', 'min:3', 'max:255', Rule::in(AppointmentTypeEnum::getAllValues())],
                'date'                => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
                'status'              => ['required', 'string', 'min:3', 'max:255', Rule::in(AppointmentStatusEnum::getAllValues())],
                'device_type'         => ['nullable', 'string', 'min:3', 'max:255'],
                'cancellation_reason' => ['string', 'nullable', Rule::requiredIf($this->input('status') == AppointmentStatusEnum::CANCELLED->value), Rule::excludeIf(fn() => auth()?->user()?->isCustomer())],
                'system_offers'       => ['array', 'nullable', Rule::excludeIf(
                    fn() => auth()->user()?->isClinic() || $this->input('type') == AppointmentTypeEnum::MANUAL->value
                )],
                'system_offers.*'     => ['numeric', 'exists:system_offers,id', new ValidSystemOffer($this->input('customer_id')), new SystemOfferBelongToClinic($this->input('clinic_id'))],
                'offers'              => ['array', 'nullable', Rule::excludeIf(fn() => auth()->user()?->isCustomer())],
                'offers.*'            => ['numeric', 'exists:offers,id', new ClinicOfferBelongToClinic($this->input('clinic_id'))],
            ];
        }

        $appointment = Appointment::find(request()->route('appointment'));

        return [
            'note'                => ['nullable', 'string'],
            'service_id'          => ['nullable', 'numeric', 'exists:services,id'],
            'extra_fees'          => ['nullable', 'numeric', 'min:0'],
            'discount'            => ['nullable', 'numeric', 'min:0'],
            'status'              => ['nullable', 'string', 'min:3', 'max:255', Rule::in(AppointmentStatusEnum::getAllValues())],
            'cancellation_reason' => 'string|nullable|' . Rule::requiredIf($this->input('status') == AppointmentStatusEnum::CANCELLED->value),

            'offers'   => ['array', 'nullable', Rule::excludeIf(fn() => auth()->user()?->isCustomer())],
            'offers.*' => [
                'numeric',
                'exists:offers,id',
                new ClinicOfferBelongToClinic($appointment?->clinic_id)
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

        if (auth()->user()?->isClinic()) {
            $this->merge([
                'clinic_id'     => auth()->user()?->getClinicId(),
                'type'          => AppointmentTypeEnum::MANUAL->value,
                'system_offers' => null,
            ]);

            if (request()->method() == "POST" && auth()?->user()?->isClinic()) {
                $this->merge([
                    'status'              => AppointmentStatusEnum::BOOKED->value,
                    'cancellation_reason' => null
                ]);
            }
        }

        if (auth()?->user()?->isCustomer()) {
            $this->merge([
                'type'        => AppointmentTypeEnum::ONLINE->value,
                'status'      => AppointmentStatusEnum::PENDING->value,
                'customer_id' => auth()?->user()?->customer?->id,
                'device_type' => str_replace(['Unknown-', '-Unknown'], "", Browser::deviceType() . '-' .
                    Browser::deviceFamily() . '-' .
                    Browser::platformFamily() . '-' .
                    Browser::deviceModel() . '-' .
                    Browser::platformName()),
            ]);
        }
    }
}
