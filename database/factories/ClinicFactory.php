<?php

namespace Database\Factories;

use App\Enums\AttendanceLogTypeEnum;
use App\Enums\RolesPermissionEnum;
use App\Enums\WeekDayEnum;
use App\Exceptions\RoleDoesNotExistException;
use App\Models\Appointment;
use App\Models\AttendanceLog;
use App\Models\Clinic;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\Speciality;
use App\Models\User;
use App\Repositories\AttendanceRepository;
use App\Services\v1\AttendanceLog\AttendanceLogService;
use App\Traits\FileHandler;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory
 */
class ClinicFactory extends Factory
{
    use FileHandler;

    /**
     * Define the model's default state.
     * @return array<string, mixed>
     * @throws RoleDoesNotExistException
     */
    public function definition(): array
    {
        return [
            'appointment_cost' => fake()->randomNumber(2),
            'user_id' => User::factory()->create()->assignRole(RolesPermissionEnum::DOCTOR['role'])->id,
            'working_start_year' => fake()->date('Y'),
            'max_appointments' => fake()->numberBetween(1, 10),
        ];
    }

    public function allRelations(): ClinicFactory
    {
        return $this->withSchedules()
            ->withSpecialities()
            ->withServices()
            ->withPrescriptions();
    }

    public function withPrescriptions($count = 1): ClinicFactory
    {
        return $this->has(Prescription::factory($count));
    }

    public function withServices($count = 1): ClinicFactory
    {
        return $this->has(Service::factory($count));
    }

    public function withSpecialities($count = 1): ClinicFactory
    {
        return $this->has(Speciality::factory($count));
    }

    public function withSchedules(): ClinicFactory
    {
        return $this->afterCreating(function (Clinic $clinic) {
            $schedules = collect();
            foreach (WeekDayEnum::getAllValues() as $day) {
                $schedules->push(Schedule::create([
                    'scheduleable_id' => $clinic->id,
                    'scheduleable_type' => Clinic::class,
                    'day_of_week' => $day,
                    'start_time' => Carbon::parse('09:00'),
                    'end_time' => Carbon::parse('21:00'),
                ]));
            }

            $dateRange = CarbonPeriod::create(now()->startOfMonth(), now()->endOfMonth());
            $attendance = collect($dateRange)
                ->flatMap(function (Carbon $date) use ($schedules, $clinic) {
                    $scheduleSlotsInDay = $schedules->groupBy('day_of_week')->get(strtolower($date->dayName)) ?? collect();
                    $attendanceAc = AttendanceRepository::make()->getByDateOrCreate($date);
                    $attendAt = Carbon::parse($date->format('Y-m-d') . ' ' . '09:00')?->format('Y-m-d H:i:s');
                    $slots[0] = [
                        'type' => AttendanceLogTypeEnum::CHECKIN->value,
                        'user_id' => $clinic->user_id,
                        'attend_at' => $attendAt,
                        'attendance_id' => $attendanceAc->id,
                        'status' => AttendanceLogService::make()->getLogStatus(Carbon::parse($attendAt), AttendanceLogTypeEnum::CHECKIN->value, $scheduleSlotsInDay),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $attendAt = Carbon::parse($date->format('Y-m-d') . ' ' . '17:00')->format('Y-m-d H:i:s');
                    $slots[1] = [
                        'user_id' => $clinic->user_id,
                        'status' => AttendanceLogService::make()->getLogStatus(Carbon::parse($attendAt), AttendanceLogTypeEnum::CHECKOUT->value, $scheduleSlotsInDay),
                        'attend_at' => $attendAt,
                        'type' => AttendanceLogTypeEnum::CHECKOUT->value,
                        'attendance_id' => $attendanceAc->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    return $slots;
                })->toArray();

            AttendanceLog::insert($attendance);
        });
    }

    public function withAppointments($count = 1): ClinicFactory
    {
        return $this->has(Appointment::factory($count));
    }

    public function withMedicines($count = 1): ClinicFactory
    {
        return $this->has(Medicine::factory($count));
    }

    public function withMedicalRecords($count = 1): ClinicFactory
    {
        return $this->has(MedicalRecord::factory($count));
    }
}
