<?php

namespace App\Exceptions;

use App\Enums\FormulaParsingFlagEnum;
use Carbon\Carbon;

class WrongOrderWhileProcessingAttendanceException extends FormulaErrorException
{
    public Carbon $date;
    public FormulaParsingFlagEnum $errorFlag = FormulaParsingFlagEnum::WRONG_ATTENDANCE_LOGS_ORDER;

    /**
     * @param Carbon|string $date
     */
    public function __construct(Carbon|string $date)
    {
        parent::__construct(null);
        $this->date = Carbon::parse($date);
        $this->message = "The attendance logs in {$this->date->format('Y-m-d')} have a wrong order , this could be happened while entering the attendance values mostly while Importing them as excel files , You need to fix the attendance logs order in this day and consider that the right order is first checkin time then the checkout time";
    }
}
