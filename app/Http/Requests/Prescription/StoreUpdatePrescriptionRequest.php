<?php

namespace App\Http\Requests\Prescription;

use App\Services\AvailableAppointmentTimeService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdatePrescriptionRequest extends FormRequest
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
        if ($this->input('next_visit')) {
            $availableTimes = AvailableAppointmentTimeService::make()->getAvailableTimeSlots(
                $this->input('clinic_id'),
                Carbon::parse($this->input('next_visit'))?->format('Y-m-d'),
            )->map->format('Y-m-d H:i')->values();
        } else {
            $availableTimes = [null];
        }

        return [
            'clinic_id' => 'required|exists:clinics,id|numeric',
            'customer_id' => 'required|exists:customers,id|numeric',
            'appointment_id' => [
                'required',
                'numeric',
                Rule::exists('appointments', 'id')
                    ->where('clinic_id', $this->input('clinic_id'))
                    ->where('customer_id', $this->input('customer_id'))
            ],
            'other_data' => 'nullable|array',
            'other_data.*.key' => 'string|min:1|max:255',
            'other_data.*.value' => 'string|min:1|max:5000',
            'next_visit' => [
                'nullable',
                'date_format:Y-m-d H:i',
                Rule::in($availableTimes->toArray()),
            ],
            'medicines' => ['array', 'nullable'],
            'medicines.*.medicine_id' => 'numeric|exists:medicines,id',
            'medicines.*.dosage' => ['string', 'max:500', 'nullable'],
            'medicines.*.dose_interval' => ['nullable', 'string', 'max:500'],
            'medicines.*.comment' => ['nullable', 'string', 'max:1500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (isDoctor()) {
            $this->merge([
                'clinic_id' => clinic()?->id
            ]);
        }
    }
}
