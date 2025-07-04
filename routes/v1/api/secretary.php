<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

Route::post('/refresh', [v1\SecretaryAuthController::class, 'refresh'])->name("refresh.token");
Route::post('/logout', [v1\SecretaryAuthController::class, 'logout'])->name("logout");
Route::post('/update-user-data', [v1\SecretaryAuthController::class, 'updateUserDetails'])->name("update.user.data");
Route::get('/me', [v1\SecretaryAuthController::class, 'userDetails'])->name('user.details');

Route::get('/notifications', [v1\NotificationController::class, 'myNotifications'])->name('notifications.index');
Route::get('/notifications/{notificationId}/read', [v1\NotificationController::class, 'markAsRead'])->name('notifications.read');
Route::get('/notifications/read-all', [v1\NotificationController::class, 'markAllAsRead'])->name('notifications.read.all');
Route::get('/notifications/unread-count', [v1\NotificationController::class, 'unreadCount'])->name('notifications.unread.count');

Route::delete('/media/{mediaId}', [v1\MediaController::class, 'delete'])->name('media.delete');

Route::get('/clinics/{clinicId}/services', [v1\ServiceController::class, 'getClinicServices'])->name('get-clinic-services');
Route::get('/services/{serviceId}', [v1\ServiceController::class, 'show'])->name('services.show');
Route::middleware(['permission:' . PermissionEnum::CLINIC_MANAGEMENT->value])
    ->group(function () {
        Route::apiResource('/clinics', v1\ClinicController::class)
            ->except(['index', 'show'])
            ->names('clinics');

        Route::controller(v1\ScheduleController::class)
            ->group(function () {
                Route::get('/clinics/{clinicId}/schedules', 'clinicSchedules')->name('clinics.schedules');
                Route::delete('clinics/{clinicId}/schedules', 'deleteAllClinicSchedules')->name('clinics.schedules.delete');
                Route::get('/users/{userId}/schedules', 'userSchedules')->name('users.schedules');
                Route::delete('users/{userId}/schedules', 'deleteUserSchedules')->name('users.schedules.delete');
                Route::post('/schedules', 'storeUpdateSchedules')->name('schedules.storeOrUpdate');
            });

        Route::post('/services/export', [v1\ServiceController::class, 'export'])->name('services.export');
        Route::post('/services/import', [v1\ServiceController::class, 'import'])->name('services.import');
        Route::get('/services/import-example', [v1\ServiceController::class, 'getImportExample'])->name('services.import.example');
        Route::apiResource('/services', v1\ServiceController::class)->except(['index', 'show'])->names('services');

        Route::post('/service-categories/export', [v1\ServiceCategoryController::class, 'export'])->name('service.categories.export');
        Route::post('/service-categories/import', [v1\ServiceCategoryController::class, 'import'])->name('service.categories.import');
        Route::get('/service-categories/import-example', [v1\ServiceCategoryController::class, 'getImportExample'])->name('service.categories.import.example');
        Route::apiResource('/service-categories', v1\ServiceCategoryController::class)->names('service.categories');

        Route::post('/specialities/export', [v1\SpecialityController::class, 'export'])->name('specialities.export');
        Route::post('/specialities/import', [v1\SpecialityController::class, 'import'])->name('specialities.import');
        Route::get('/specialities/import-example', [v1\SpecialityController::class, 'getImportExample'])->name('specialities.import.example');
        Route::apiResource('/specialities', v1\SpecialityController::class)->names('specialities');
    });
Route::get('/clinics', [v1\ClinicController::class, 'index'])->name('clinic.index');
Route::get('/clinics/{clinicId}', [v1\ClinicController::class, 'show'])->name('clinic.show');
Route::get('/services', [v1\ServiceController::class, 'index'])->name('services.index');

Route::get('/holidays/active', [v1\HolidayController::class, 'activeHolidays'])->name('holidays.active');
Route::get('holidays', [v1\HolidayController::class, 'index'])->name('holidays.index');

Route::middleware(['permission:' . PermissionEnum::HOLIDAYS_MANAGEMENT->value])
    ->group(function () {
        Route::post('/holidays/export', [v1\HolidayController::class, 'export'])->name('holidays.export');
        Route::post('/holidays/import', [v1\HolidayController::class, 'import'])->name('holidays.import');
        Route::get('/holidays/get-import-example', [v1\HolidayController::class, 'getImportExample'])->name('holidays.get.example');
        Route::apiResource('/holidays', v1\HolidayController::class)->except(['index'])->names('holidays');
    });

Route::get('/attendances', [v1\AttendanceLogController::class, 'myAttendance'])->name('attendances.index');
Route::get('/attendances/checkin', [v1\AttendanceLogController::class, 'checkin'])->name('attendances.checkin');
Route::get('/attendances/checkout', [v1\AttendanceLogController::class, 'checkout'])->name('attendances.checkout');
Route::get('/attendances/latest', [v1\AttendanceLogController::class, 'latestLog'])->name('attendances.latest');
Route::get('/attendances/export/mine', [v1\AttendanceLogController::class, 'exportMine'])->name('attendances.export.mine');
Route::get('/attendances/statistics', [v1\AttendanceLogController::class, 'myStatistics'])->name('attendances.statistics');

Route::get('/users/employees', [v1\UserController::class, 'employees'])
    ->middleware([
        'permission:' . PermissionEnum::VACATION_MANAGEMENT->value
        . '|' . PermissionEnum::ASSETS_MANAGEMENT->value
        . '|' . PermissionEnum::ATTENDANCE_MANAGEMENT->value
        . '|' . PermissionEnum::PAYROLL_MANAGEMENT->value,
    ])->name('users.employees');

Route::middleware(['permission:' . PermissionEnum::ATTENDANCE_MANAGEMENT->value])
    ->group(function () {
        Route::post('users/{userId}/attendances', [v1\AttendanceLogController::class, 'editOrCreate'])->name('users.attendance.edit');
        Route::get('users/attendances', [v1\UserController::class, 'allWithAttendanceByDate'])->name('users.attendance.index');
        Route::get('/attendances/import-example', [v1\AttendanceLogController::class, 'getImportExample'])->name('attendances.import.example');
        Route::post('/attendances/import', [v1\AttendanceLogController::class, 'import'])->name('attendances.import');
        Route::get('/attendances/export', [v1\AttendanceLogController::class, 'export'])->name('attendances.export');
    });

Route::get('/schedule', [v1\ScheduleController::class, 'mySchedule'])->name('schedule.mine');

Route::post('/payslips/{payslipId}/toggle-status', [v1\PayslipController::class, 'toggleStatus'])->name('payslips.toggle.status');
Route::get('/payslips/{payslipId}/pdf', [v1\PayslipController::class, 'toPdf'])->name('payslips.pdf');
Route::get('/payslips', [v1\PayslipController::class, 'mine'])->name('payslips.mine');
Route::get('/payslips/{payslipId}', [v1\PayslipController::class, 'show'])->name('payslips.show');

Route::middleware(['permission:' . PermissionEnum::PAYROLL_MANAGEMENT->value])
    ->group(function () {
        Route::apiResource('/formulas', v1\FormulaController::class)->names('formulas');

        Route::apiResource('formula-variables', v1\FormulaVariableController::class)->only(['show', 'index'])->names('formula.variables');

        Route::delete('/payslip-adjustments/{payslipAdjustmentId}', [v1\PayslipAdjustmentController::class, 'destroy'])->name('payslip.adjustments.destroy');

        Route::post('/payslips/payslip-adjustments/bulk', [v1\PayslipController::class, 'bulkAdjustment'])->name('payslips.payslip.adjustments.bulk');
        Route::post('/payslips/{payslipId}/payslip-adjustments', [v1\PayslipController::class, 'addAdjustment'])->name('payslips.payslip.adjustments.store');
        Route::put('/payslips/{payslipId}', [v1\PayslipController::class, 'update'])->name('payslips.update');
        Route::get('/payruns/{payrunId}/payslips', [v1\PayslipController::class, 'getByPayrun'])->name('payruns.payslips');
        Route::post('/payslips/bulk-download', [v1\PayslipController::class, 'bulkPdfDownload'])->name('payslips.bulk.download');

        Route::get('/payruns/{payrunId}/reprocess', [v1\PayrunController::class, 'reprocessPayrun'])->name('payruns.reprocess');
        Route::get('/payruns/{payrunId}/export', [v1\PayrunController::class, 'reportToExcel'])->name('payruns.export');
        Route::post('/payruns/{payrunId}/toggle-status', [v1\PayrunController::class, 'toggleStatus'])->name('payruns.toggle.status');
        Route::apiResource('/payruns', v1\PayrunController::class)->except(['update'])->names('payruns');
    });

Route::get('/users/{userId}/vacations/active', [v1\VacationController::class, 'activeByUser'])->name('users.vacations.active');
Route::get('/users/{userId}/vacations', [v1\VacationController::class, 'byUser'])->name('users.vacations');
Route::middleware(['permission:' . PermissionEnum::VACATION_MANAGEMENT->value])
    ->group(function () {
        Route::put('/vacations/{vacationId}', [v1\VacationController::class, 'update'])->name('vacations.update');
        Route::post('/vacations/toggle-status', [v1\VacationController::class, 'toggleStatus'])->name('vacations.toggle.status');
    });
Route::get('/vacations/active', [v1\VacationController::class, 'myActiveVacations'])->name('vacations.active');
Route::apiResource('vacations', v1\VacationController::class)->except(['update'])->names('vacations');

Route::middleware(['permission:' . PermissionEnum::MEDICINE_MANAGEMENT->value])
    ->group(function () {
        Route::get('/prescriptions/{prescriptionId}/to-pdf', [v1\PrescriptionController::class, 'toPdf'])->name('prescriptions.to.pdf');
        Route::get('/prescriptions/{prescriptionId}', [v1\PrescriptionController::class, 'show'])->name('prescriptions.show');
        Route::get('/customers/{customerId}/prescriptions', [v1\PrescriptionController::class, 'getByCustomer'])->name('customers.prescriptions');
        Route::get('/medicine-prescriptions/{medicinePrescriptionId}/toggle-status', [v1\MedicinePrescriptionController::class, 'toggleStatus'])->name('medicine.prescriptions.toggle.status');
        Route::post('/medicines/export', [v1\MedicineController::class, 'export'])->name('medicines.export');
        Route::post('/medicines/import', [v1\MedicineController::class, 'import'])->name('medicines.import');
        Route::get('/medicines/get-import-example', [v1\MedicineController::class, 'getImportExample'])->name('medicines.get.example');
        Route::apiResource('/medicines', v1\MedicineController::class)->names('medicines');
    });

Route::get('/customers', [v1\CustomerController::class, 'index'])->name('customers.index');
Route::middleware(['permission:' . PermissionEnum::PATIENT_MANAGEMENT->value])
    ->group(function () {
        Route::get('/customers/{customerId}/pdf-report', [v1\CustomerController::class, 'pdfReport'])->name('customers.pdf.report');
        Route::get('/customers/{customerId}/appointments', [v1\AppointmentController::class, 'getByCustomer'])
            ->middleware(['permission:' . PermissionEnum::APPOINTMENT_MANAGEMENT->value])
            ->name('clinics.appointments');
        Route::post('/media/customers/attachments', [v1\MediaController::class, 'addCustomerAttachment'])->name('media.customers.attachments.store');
        Route::get('/customers/{customerId}/medical-records', [v1\MedicalRecordController::class, 'getByCustomer'])->name('customers.medical.records');
        Route::get('/customers/recent', [v1\CustomerController::class, 'getRecent'])->name('customers.recent');
        Route::apiResource('/customers', v1\CustomerController::class)->except(['index'])->names('customers');

        Route::post('/patient-studies', [v1\PatientStudyController::class, 'store'])->name('patient.studies.store');
        Route::delete('patient-studies/{patientStudyId}', [v1\PatientStudyController::class, 'destroy'])->name('patient.studies.destroy');
    });

Route::middleware(['permission:' . PermissionEnum::APPOINTMENT_MANAGEMENT->value])
    ->group(function () {
        Route::get('appointment-logs/{appointmentLogId}', [v1\AppointmentLogController::class, 'show'])->name('appointment.log.show');
        Route::get('appointments/{appointmentId}/logs', [v1\AppointmentLogController::class, 'getAppointmentLogs'])->name('appointments.logs');

        Route::post('appointments/export', [v1\AppointmentController::class, 'export'])->name('appointments.export');
        Route::put('appointments/change-status', [v1\AppointmentController::class, 'changeAppointmentStatus'])->name('appointments.change.status');

        Route::get('/clinics/{clinicId}/appointments', [v1\AppointmentController::class, 'getByClinic'])
            ->middleware(['permission:' . PermissionEnum::CLINIC_MANAGEMENT->value])
            ->name('clinics.appointments');

        Route::apiResource('appointments', v1\AppointmentController::class)->except(['destroy'])->names('appointments');
    });

Route::post('clinics/available-appointments-times', [v1\AvailableAppointmentTimeController::class, 'get'])->name('clinics.available.appointments.time');


Route::middleware(['permission:' . PermissionEnum::TRANSACTION_MANAGEMENT->value])
    ->group(function () {
        Route::get('/transactions/balance', [v1\TransactionController::class, 'balance'])->name('transactions.balance');
        Route::apiResource('/transactions', v1\TransactionController::class)->names('transactions');
    });

Route::post('/tasks/change-status', [v1\TaskController::class, 'changeStatus'])->name('tasks.change.status');
Route::get('/tasks/mine', [v1\TaskController::class, 'mine'])->name('tasks.mine');
Route::get('/tasks/{taskId}', [v1\TaskController::class, 'show'])->name('tasks.show');
Route::apiResource('/tasks', v1\TaskController::class)
    ->except(['show'])
    ->middleware(['permission:' . PermissionEnum::TASKS_MANAGEMENT->value])
    ->names('tasks');

Route::apiResource('/task-comments', v1\TaskCommentController::class)->only(['store', 'update', 'destroy'])->names('task.comments');

Route::get('/user-assets/mine', [v1\UserAssetController::class, 'assignedToMe'])->name('assets.mine');
Route::get('/assets/{assetId}', [v1\AssetController::class, 'show'])->name('assets.show');
Route::middleware(['permission:' . PermissionEnum::ASSETS_MANAGEMENT->value])
    ->group(function () {
        Route::get('/users/{userId}/user-assets', [v1\UserAssetController::class, 'getAssignedByUser'])->name('users.user.assets');
        Route::get('/assets/{assetId}/user-assets', [v1\UserAssetController::class, 'getAssignedByAsset'])->name('assets.user.assets');
        Route::post('/assets/checkin', [v1\AssetController::class, 'checkin'])->name('assets.checkin');
        Route::post('/assets/checkout', [v1\AssetController::class, 'checkout'])->name('assets.checkout');
        Route::apiResource('/assets', v1\AssetController::class)->except(['show'])->names('assets');
    });
