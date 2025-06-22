<?php

namespace App\Enums;

use App\Traits\BaseEnum;

enum PermissionEnum: string
{
    use BaseEnum;

    case HOLIDAYS_MANAGEMENT = "holidays management";
    case ATTENDANCE_MANAGEMENT = "attendance management";
    case PAYROLL_MANAGEMENT = "payroll management";
    case VACATION_MANAGEMENT = "vacation management";
    case PATIENT_MANAGEMENT = "patient management";
    case APPOINTMENT_MANAGEMENT = "appointment management";
    case MEDICINE_MANAGEMENT = "medicine management";
    case CLINIC_MANAGEMENT = "clinic management";
    case TRANSACTION_MANAGEMENT = "transaction management";
    case TASKS_MANAGEMENT = "tasks management";
}
