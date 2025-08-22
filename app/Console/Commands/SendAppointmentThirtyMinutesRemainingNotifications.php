<?php

namespace App\Console\Commands;

use App\Enums\AppointmentStatusEnum;
use App\Models\Appointment;
use App\Modules\Notification\App\Enums\NotifyMethod;
use App\Modules\Notification\App\NotificationBuilder;
use App\Notifications\Customer\AppointmentRemainingTimeNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SendAppointmentThirtyMinutesRemainingNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-appointment-thirty-minutes-remaining-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Appointment::where('status', AppointmentStatusEnum::BOOKED->value)
            ->where('date_time', now()->addMinutes(30))
            ->with(['clinic.user', 'customer', 'customer.user'])
            ->chunk(100, function (Collection $appointments) {
                $appointments->each(function (Appointment $appointment) {
                    NotificationBuilder::make()
                        ->method(NotifyMethod::ONE)
                        ->data([
                            'appointment' => $appointment,
                        ])->notification(AppointmentRemainingTimeNotification::class)
                        ->to($appointment->customer->user)
                        ->send();
                });
            });
    }
}
