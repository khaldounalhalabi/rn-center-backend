<?php

namespace App\Http\Requests\v1\Appointment;

use App\Enums\AppointmentStatusEnum;
use App\Enums\AppointmentTypeEnum;
use App\Enums\PermissionEnum;
use App\Repositories\AppointmentRepository;
use App\Services\AvailableAppointmentTimeService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
        if (isDoctor()) {
            return [
                'clinic_id' => ['nullable', Rule::requiredIf($this->isPost()), Rule::excludeIf($this->isPut()), 'numeric', 'exists:clinics,id'],
                'note' => 'nullable|string|max:10000',
                'service_id' => ['nullable', 'numeric', Rule::exists('services', 'id')->where('clinic_id', $this->input('clinic_id'))],
            ];
        }

        if (!$this->input('clinic_id') || !$this->input('date_time')) {
            $availableTimes = collect();
        } elseif ($this->isPost()) {
            $availableTimes = AvailableAppointmentTimeService::make()->getAvailableTimeSlots(
                $this->input('clinic_id'),
                Carbon::parse($this->input('date_time'))?->format('Y-m-d'),
            )->map->format('Y-m-d H:i')->values();
        } else {
            $requestedDate = Carbon::parse($this->input('date_time'));
            $appointment = AppointmentRepository::make()->find($this->route('appointment'));
            if (!$appointment) {
                throw ValidationException::withMessages([
                    'appointment_id' => 'Invalid Appointment'
                ]);
            }
            $availableTimes = AvailableAppointmentTimeService::make()->getAvailableTimeSlots(
                $appointment->clinic_id,
                $requestedDate->format('Y-m-d')
            )->map->format('Y-m-d H:i')->values();

            if ($appointment->date_time->format('Y-m-d') == $requestedDate->format('Y-m-d')) {
                $availableTimes = $availableTimes->push($appointment->date_time?->format('Y-m-d H:i'));
            }
        }

        if (isCustomer()) {
            return [
                'customer_id' => ['nullable', Rule::requiredIf($this->isPost()), Rule::excludeIf($this->isPut()), 'numeric', 'exists:customers,id'],
                'clinic_id' => ['nullable', Rule::requiredIf($this->isPost()), Rule::excludeIf($this->isPut()), 'numeric', 'exists:clinics,id'],
                'service_id' => ['nullable', 'numeric', Rule::exists('services', 'id')->where('clinic_id', $this->input('clinic_id'))],
                'date_time' => [
                    'required',
                    'date_format:Y-m-d H:i',
                    Rule::in($availableTimes->toArray()),
                ],
                'type' => ['string', Rule::in(AppointmentTypeEnum::getAllValues())],
            ];
        }

        return [
            'customer_id' => ['nullable', Rule::requiredIf($this->isPost()), Rule::excludeIf($this->isPut()), 'numeric', 'exists:customers,id'],
            'clinic_id' => ['nullable', Rule::requiredIf($this->isPost()), Rule::excludeIf($this->isPut()), 'numeric', 'exists:clinics,id'],
            'note' => 'nullable|string|max:10000',
            'service_id' => ['nullable', 'numeric', Rule::exists('services', 'id')->where('clinic_id', $this->input('clinic_id'))],
            'extra_fees' => ['nullable', 'numeric', 'min:0'],
            'type' => ['string', Rule::in(AppointmentTypeEnum::getAllValues())],
            'date_time' => [
                'required',
                'date_format:Y-m-d H:i',
                Rule::in($availableTimes->toArray()),
            ],
            'status' => ['required', 'string', Rule::in(AppointmentStatusEnum::getAllValues())],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'cancellation_reason' => ['nullable', 'string', 'max:10000', 'required_if:status,' . AppointmentStatusEnum::CANCELLED->value],
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
            ]);
        }

        if ((isAdmin() || can(PermissionEnum::APPOINTMENT_MANAGEMENT)) && $this->isPost()) {
            $this->merge([
                'type' => AppointmentTypeEnum::MANUAL->value,
            ]);
        }

        if (isCustomer() && $this->isPost()) {
            $this->merge([
                'type' => AppointmentTypeEnum::ONLINE->value,
                'status' => AppointmentStatusEnum::PENDING->value,
                'customer_id' => customer()?->id,
            ]);
        }
    }
}
