<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\AppointmentStatusEnum;
use App\Enums\ClinicTransactionTypeEnum;
use App\Http\Controllers\ApiController;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class StatisticsController extends ApiController
{
    public function doctorIndexStatistics()
    {
        $clinic = auth()->user()?->getClinic();

        $data = Appointment::selectRaw(
            "
                       COUNT(*) as total_this_month,
                       SUM(IF(type = 'online', 1, 0)) as total_online_this_month,
                       SUM(IF(status = 'cancelled', 1, 0)) as total_cancelled_this_month
                      ")
            ->where('date', '>=', now()->firstOfMonth()->format('Y-m-d'))
            ->where('date', '<=', now()->lastOfMonth()->format('Y-m-d'))
            ->where('clinic_id', $clinic?->id)
            ->get()
            ->first()
            ->toArray();

        $data['total_upcoming'] = $clinic?->upcomingAppointments()->count() ?? 0;

        $data['total_appointments'] = $clinic?->appointments()
            ->whereNotIn('status', [AppointmentStatusEnum::CANCELLED->value, AppointmentStatusEnum::PENDING->value])
            ->count() ?? 0;

        $data['today_appointments'] = $clinic?->appointments()
            ->whereDate('date', now()->format('Y-m-d'))
            ->count() ?? 0;

        $data['upcoming_appointments'] = $clinic?->upcomingAppointments()->count();
        $data['total_income_current_month'] = $clinic?->clinicTransactions()
            ->where('type', ClinicTransactionTypeEnum::INCOME->value)
            ->where('date', '>=', now()->firstOfMonth()->format('Y-m-d'))
            ->where('date', '<=', now()->lastOfMonth()->format('Y-m-d'))
            ->sum('amount') ?? 0;
        $data['total_income_prev_month'] = $clinic?->clinicTransactions()
            ->where('type', ClinicTransactionTypeEnum::INCOME->value)
            ->where('date', '>=', now()->subMonth()->firstOfMonth()->format('Y-m-d'))
            ->where('date', '<=', now()->subMonth()->lastOfMonth()->format('Y-m-d'))
            ->sum('amount') ?? 0;

        return $this->apiResponse(
            array_map(fn ($item) => is_null($item) ? 0 : floatval($item), $data),
            self::STATUS_OK,
            __('site.get_successfully')
        );
    }

    public function adminStatistics()
    {
        $data = DB::select(
            "
                    SELECT
                        (SELECT COUNT(*) FROM appointments) AS total_appointments,
                        (SELECT COUNT(*) FROM appointments WHERE date >= ?) AS upcoming_appointments,
                        (SELECT SUM(amount) FROM appointment_deductions WHERE date >= ? AND date <= ?) AS total_deductions_current_month,
                        (SELECT SUM(amount) FROM appointment_deductions WHERE date >= ? AND date <= ?) AS total_deductions_prev_month
                  ",
            [
                now()->format('Y-m-d'),
                now()->firstOfMonth()->format('Y-m-d'),
                now()->lastOfMonth()->format('Y-m-d'),
                now()->subMonth()->firstOfMonth()->format('Y-m-d'),
                now()->subMonth()->lastOfMonth()->format('Y-m-d'),
            ]);

        return $this->apiResponse(
            collect($data[0] ?? [])->map(fn ($value) => $value === null ? 0 : $value),
            self::STATUS_OK,
            __('site.get_successfully')
        );
    }
}
