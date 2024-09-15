<?php

namespace App\Jobs;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class UpdateAppointmentRemainingTimeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $clinicId;
    private Carbon $appointmentDate;

    /**
     * Create a new job instance.
     */
    public function __construct(int $clinicId, Carbon $appointmentDate)
    {
        $this->clinicId = $clinicId;
        $this->appointmentDate = $appointmentDate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Updating Appointments Remaining Time Has Been Dispatched");

        Appointment::where('date', $this->appointmentDate->format('Y-m-d'))
            ->where('clinic_id', $this->clinicId)
            ->validNotEnded()
            ->chunk(5, function (Collection /** @var Collection<Appointment> $appointments */ $appointments) {
                Log::info(print_r($appointments->toArray(), 1));
                foreach ($appointments as $appointment) {
                    Appointment::handleRemainingTime($appointment)->saveQuietly();
                }
            });
    }
}
