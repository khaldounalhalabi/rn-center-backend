<?php

namespace App\Console\Commands;

use App\Enums\AttendanceLogTypeEnum;
use App\Models\AttendanceLog;
use App\Repositories\AttendanceLogRepository;
use App\Repositories\AttendanceRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixAttendanceLogsSequence extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'app:fix-attendance-logs-sequence {from} {to}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Fix attendance logs with the next event type and timestamp for each employee, enabling sequence analysis and time tracking.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $from = $this->argument('from');
        $to = $this->argument('to');
        $this->fixAttendanceLogsForCompany($from, $to);
    }

    public function fixAttendanceLogsForCompany(string $from, string $to): void
    {
        $from = Carbon::parse($from)->format('Y-m-d');
        $to = Carbon::parse($to)->format('Y-m-d');

        DB::table(DB::raw("
                (SELECT
                    id,
                    attendance_id,
                    user_id,
                    attend_at,
                    type,
                    status,
                    LEAD(type) OVER (PARTITION BY user_id ORDER BY attend_at) AS next_type,
                    LEAD(attend_at) OVER (PARTITION BY user_id ORDER BY attend_at) AS next_datetime
                 FROM attendance_logs WHERE DATE(attend_at) >= '{$from}' AND DATE(attend_at) <= '{$to}'
                ) as ordered_records"))
            ->where('type', AttendanceLogTypeEnum::CHECKIN->value)
            ->where('next_type', AttendanceLogTypeEnum::CHECKOUT->value)
            ->whereRaw('DATE(next_datetime) = DATE(attend_at) + INTERVAL 1 DAY')
            ->orderBy('attend_at')
            ->cursor()
            ->each(function ($log) {
                AttendanceLog::withoutEvents(function () use ($log) {
                    $log = new AttendanceLog((array)$log);
                    AttendanceLogRepository::make()->create([
                        'type' => AttendanceLogTypeEnum::CHECKOUT->value,
                        'user_id' => $log->user_id,
                        'attendance_id' => $log->attendance_id,
                        'attend_at' => $log->attend_at->endOfDay(),
                        'status' => $log->status,
                    ]);

                    AttendanceLogRepository::make()->create([
                        'type' => AttendanceLogTypeEnum::CHECKIN->value,
                        'user_id' => $log->user_id,
                        'attendance_id' => AttendanceRepository::make()->getByDateOrCreate($log->attend_at->addDay())->id,
                        'attend_at' => $log->attend_at->addDay()->startOfDay(),
                        'status' => $log->status,
                    ]);
                });
            });
    }
}
