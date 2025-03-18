<?php

namespace App\Http\Resources;

use App\Enums\SubscriptionStatusEnum;
use App\Models\Clinic;

/** @mixin Clinic */
class ClinicResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'                           => $this->id,
            'name'                         => $this->name,
            'appointment_cost'             => $this->appointment_cost,
            'user_id'                      => $this->user_id,
            'hospital_id'                  => $this->hospital_id,
            'working_start_year'           => $this->working_start_year->format('Y-m-d'),
            'experience_years'             => now()->diffInYears($this->working_start_year),
            'max_appointments'             => $this->max_appointments,
            'appointment_day_range'        => $this->appointment_day_range,
            'status'                       => $this->status,
            'about_us'                     => $this->about_us,
            'experience'                   => $this->experience,
            'created_at'                   => $this->created_at->format('Y-m-d'),
            'updated_at'                   => $this->updated_at->format('Y-m-d'),
            'agreed_on_contract'           => $this->agreed_on_contract,
            'approximate_appointment_time' => $this->approximate_appointment_time,
            'hospital'                     => new HospitalResource($this->whenLoaded('hospital')),
            'user'                         => new UserResource($this->whenLoaded('user')),
            'schedules'                    => new ScheduleCollection($this->whenLoaded('schedules')),
            'clinicHolidays'               => ClinicHolidayResource::collection($this->whenLoaded('clinicHolidays')),
            'specialities'                 => SpecialityResource::collection($this->whenLoaded('specialities')),
            'services'                     => ServiceResource::collection($this->whenLoaded('services')),
            'appointments'                 => AppointmentResource::collection($this->whenLoaded('appointments')),
            'work_gallery'                 => MediaResource::collection($this->whenLoaded('media')),
            'offers'                       => OfferResource::collection($this->whenLoaded('offers')),
            'systemOffers'                 => SystemOfferResource::collection($this->whenLoaded('systemOffers')),
            'reviews'                      => ReviewResource::collection($this->whenLoaded('reviews')),
            $this->mergeWhen($this->relationLoaded('reviews'), [
                'rate' => $this->reviews->count() > 0 ? round($this->reviews->sum('rate') / $this->reviews->count(), 1) : 0,
            ]),
            
            $this->mergeWhen(auth()?->user()?->isAdmin() || auth()?->user()?->isClinic(), [
                'total_appointments'          => $this->whenCounted('appointments'),
                'today_appointments_count'    => $this->whenCounted('todayAppointments'),
                'upcoming_appointments_count' => $this->whenCounted('upcomingAppointments'),
                'last_subscription'           => new ClinicSubscriptionResource($this->whenLoaded('lastSubscription')),
                'active_subscription'         => new ClinicSubscriptionResource($this->whenLoaded('activeSubscription')),
                'appointment_deductions'      => AppointmentDeductionResource::collection($this->whenLoaded('appointmentDeductions')),
                'patientProfiles'             => PatientProfileResource::collection($this->whenLoaded('patientProfiles')),
                'clinicEmployees'             => ClinicEmployeeResource::collection($this->whenLoaded('clinicEmployees')),
                'prescriptions'               => PrescriptionResource::collection($this->whenLoaded('prescriptions')),
                'medicines'                   => MedicineResource::collection($this->whenLoaded('medicines')),
                'clinic_transactions'         => ClinicTransactionResource::collection($this->whenLoaded('clinicTransactions')),
                'subscription_status'         => $this->activeSubscription ? SubscriptionStatusEnum::ACTIVE->value : SubscriptionStatusEnum::IN_ACTIVE->value,
            ]),
        ];
    }
}
