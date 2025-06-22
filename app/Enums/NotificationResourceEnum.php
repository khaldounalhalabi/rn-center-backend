<?php

namespace App\Enums;

enum NotificationResourceEnum: string
{
    case APPOINTMENT = 'appointment';
    case VACATION = 'vacation';
    case PAYRUN = 'payrun';
    case PAYSLIP = 'payslip';
    case TASK = 'task';
}
