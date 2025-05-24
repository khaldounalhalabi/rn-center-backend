<?php

namespace App\FormulaParser\SystemVariables\ClinicVariables;

use App\Enums\AppointmentStatusEnum;
use App\FormulaParser\SystemVariables\SystemVariable;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class CompletedAppointments extends SystemVariable
{
    private User $user;
    private Carbon $from;
    private Carbon $to;

    /**
     * @param User          $user
     * @param Carbon|string $from
     * @param Carbon|string $to
     */
    public function __construct(User $user, Carbon|string $from, Carbon|string $to)
    {
        $this->user = $user;
        $this->from = Carbon::parse($from);
        $this->to = Carbon::parse($to);
    }

    public function getResult(): int|float|bool
    {
        return Clinic::where('user_id', $this->user->id)
            ->whereHas('appointments', function (Appointment|Builder $query) {
                $query->where('status', AppointmentStatusEnum::CHECKOUT->value)
                    ->orWhere('status', AppointmentStatusEnum::COMPLETED->value)
                    ->whereDate('date_time', '>=', $this->from->format('Y-m-d'))
                    ->whereDate('date_time', '<=', $this->to->format('Y-m-d'));
            })->count();
    }
}
