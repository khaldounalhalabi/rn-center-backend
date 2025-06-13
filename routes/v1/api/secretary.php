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
