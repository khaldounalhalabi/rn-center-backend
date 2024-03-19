<?php

namespace App\Rules;

use App\Models\Clinic;
use App\Models\Hospital;
use App\Models\Schedule;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class UniqueSchedule implements ValidationRule
{
    private ?array $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        if (isset($data['schedule_id'])) {
            $oldSchedule = Schedule::find($data['schedule_id']);
            $this->data['schedulable_id'] = $data['clinic_id'] ?? $data['hospital_id'] ?? $oldSchedule->schedulable_id;
            $this->data['schedulable_type'] = $oldSchedule->schedulable_type;
            $this->data['start_time'] = $data['start_time'] ?? $oldSchedule->start_time;
            $this->data['end_time'] = $data['end_time'] ?? $oldSchedule->end_time;
            $this->data['day_of_week'] = $data['day_of_week'] ?? $oldSchedule->day_of_week;

        } elseif (isset($data['clinic_id'])) {
            $this->data['schedulable_type'] = Clinic::class;
            $this->data['schedulable_id'] = $data['clinic_id'];
        } elseif (isset($data['hospital_id'])) {
            $this->data['schedulable_type'] = Hospital::class;
            $this->data['schedulable_id'] = $data['hospital_id'];
        } else $this->data = null;
    }

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->data) {
            $fail(__('site.uncompleted_schedule_data'));
        }

        $schedule = Schedule::where('schedulable_id', $this->data['schedulable_id'])
            ->where('schedulable_type', $this->data['schedulable_type'])
            ->where('start_time', $this->data['start_time'])
            ->where('end_time', $this->data['end_time'])
            ->where('day_of_week', $this->data['day_of_week'])
            ->first();

        if ($schedule) {
            $fail(__('site.duplicated_schedule'));
        }
    }
}
