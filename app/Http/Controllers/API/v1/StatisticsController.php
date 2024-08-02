<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\ApiController;
use App\Models\Appointment;

class StatisticsController extends ApiController
{
    public function doctorIndexStatistics()
    {
        $counts = Appointment::selectRaw("COUNT(*) as total_this_month,SUM(IF(type = 'online', 1, 0)) as total_online_this_month,SUM(IF(status = 'cancelled', 1, 0)) as total_cancelled_this_month")
            ->where('date', now()->format('Y-m-d'))
            ->where('clinic_id', auth()->user()?->getClinicId())
            ->get()
            ->first()
            ->toArray();
        $counts['total_upcoming'] = auth()->user()?->clinic->upcomingAppointments()->count();

        return $this->apiResponse(
            array_map(fn ($item) => is_null($item) ? 0 : $item, $counts),
            self::STATUS_OK,
            __('site.get_successfully')
        );
    }
}
