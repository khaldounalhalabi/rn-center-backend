<?php

namespace Database\Factories;

use App\Enums\AttendanceLogTypeEnum;
use App\Enums\GenderEnum;
use App\Enums\RolesPermissionEnum;
use App\Enums\WeekDayEnum;
use App\Models\AttendanceLog;
use App\Models\Clinic;
use App\Models\Customer;
use App\Models\Formula;
use App\Models\Payslip;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Vacation;
use App\Repositories\AttendanceRepository;
use App\Services\v1\AttendanceLog\AttendanceLogService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => '123456789',
            'remember_token' => Str::random(10),
            'phone' => '09' . fake()->unique()->randomNumber(8, true),
            'gender' => fake()->randomElement(GenderEnum::getAllValues()),
        ];
    }

    public function verified(): Factory|UserFactory
    {
        return $this->state([
            'phone_verified_at' => now(),
        ]);
    }

    public function customer(): UserFactory
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(RolesPermissionEnum::CUSTOMER['role']);
            Customer::factory()->create([
                'user_id' => $user->id,
            ]);
        });
    }

    public function clinic(): UserFactory
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(RolesPermissionEnum::DOCTOR['role']);
            $user->update([
                'formula_id' => Formula::inRandomOrder()->first()?->id ?? Formula::factory()->create()->id,
            ]);
            Clinic::factory()->withSchedules()->create([
                'user_id' => $user->id,
            ]);
        });
    }

    public function admin(): UserFactory
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(RolesPermissionEnum::ADMIN['role']);
        });
    }

    public function withSchedules(): UserFactory
    {
        return $this->afterCreating(function (User $user) {
            $schedules = collect();
            foreach (WeekDayEnum::getAllValues() as $day) {
                $schedules->push(Schedule::create([
                    'scheduleable_id' => $user->id,
                    'scheduleable_type' => User::class,
                    'day_of_week' => $day,
                    'start_time' => Carbon::parse('09:00'),
                    'end_time' => Carbon::parse('21:00'),
                ]));
            }

            $dateRange = CarbonPeriod::create(now()->startOfMonth(), now()->endOfMonth());
            $attendance = collect($dateRange)
                ->flatMap(function (Carbon $date) use ($schedules, $user) {
                    $scheduleSlotsInDay = $schedules->groupBy('day_of_week')->get($date->dayOfWeek) ?? collect();
                    $attendanceAc = AttendanceRepository::make()->getByDateOrCreate($date);
                    $attendAt = Carbon::parse($date->format('Y-m-d') . ' ' . '09:00')?->format('Y-m-d H:i:s');
                    $slots[0] = [
                        'type' => AttendanceLogTypeEnum::CHECKIN->value,
                        'user_id' => $user->id,
                        'attend_at' => $attendAt,
                        'attendance_id' => $attendanceAc->id,
                        'status' => AttendanceLogService::make()->getLogStatus(Carbon::parse($attendAt), AttendanceLogTypeEnum::CHECKIN->value, $scheduleSlotsInDay),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $attendAt = Carbon::parse($date->format('Y-m-d') . ' ' . '17:00')->format('Y-m-d H:i:s');
                    $slots[1] = [
                        'user_id' => $user->id,
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

    public function withPayslips($count = 1): UserFactory
    {
        return $this->has(Payslip::factory($count));
    }

    public function secretary(): UserFactory
    {
        return $this->withSchedules()
            ->afterCreating(function (User $user) {
                $user->assignRole(RolesPermissionEnum::SECRETARY['role']);
                $user->update([
                    'formula_id' => Formula::inRandomOrder()->first()?->id ?? Formula::factory()->create()->id,
                ]);
            });
    }

    public function withVacations($count = 1): UserFactory
    {
        return $this->has(Vacation::factory($count));
    }
}
