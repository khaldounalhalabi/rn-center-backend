<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

Route::post('/refresh', [v1\AdminAuthController::class, 'refresh'])->name('refresh.token');
Route::post('/logout', [v1\AdminAuthController::class, 'logout'])->name('logout');
Route::post('/update-user-data', [v1\AdminAuthController::class, 'updateUserDetails'])->name('update.user.data');
Route::get('/me', [v1\AdminAuthController::class, 'userDetails'])->name('user.detSails');
Route::post('/fcm/store-token', [v1\AdminAuthController::class, 'storeFcmToken'])->name('fcm.storeToken');
Route::get('/fcm/get-token', [v1\AdminAuthController::class, 'getUserFcmToken'])->name('fcm.getToken');

Route::get('notifications', [v1\NotificationController::class, 'getUserNotification'])->name('notifications');
Route::get('notifications/unread/count', [v1\NotificationController::class, 'unreadCount'])->name('notification.unread.count');
Route::get('/notifications/{notificationId}/mark-as-read', [v1\NotificationController::class, 'markAsRead'])->name('notifications');

Route::apiResource('/clinics', v1\ClinicController::class)->names('clinics');

Route::controller(v1\ScheduleController::class)
    ->group(function () {
        Route::get('/clinics/{clinicId}/schedules', 'clinicSchedules')->name('clinics.schedules');
        Route::delete('clinics/{clinicId}/schedules', 'deleteAllClinicSchedules')->name('clinics.schedules.delete');
        Route::get('/users/{userId}/schedules', 'userSchedules')->name('users.schedules');
        Route::delete('users/{userId}/schedules', 'deleteUserSchedules')->name('users.schedules.delete');
        Route::post('/schedules', 'storeUpdateSchedules')->name('schedules.storeOrUpdate');
    });

Route::get('/customers/recent', [v1\CustomerController::class, 'getRecent'])->name('customers.recent');
Route::apiResource('/customers', v1\CustomerController::class)->names('customers');

Route::post('/specialities/export', [v1\SpecialityController::class, 'export'])->name('specialities.export');
Route::post('/specialities/import', [v1\SpecialityController::class, 'import'])->name('specialities.import');
Route::get('/specialities/import-example', [v1\SpecialityController::class, 'getImportExample'])->name('specialities.import.example');
Route::apiResource('/specialities', v1\SpecialityController::class)->names('specialities');

Route::post('/service-categories/export', [v1\ServiceCategoryController::class, 'export'])->name('service.categories.export');
Route::post('/service-categories/import', [v1\ServiceCategoryController::class, 'import'])->name('service.categories.import');
Route::get('/service-categories/import-example', [v1\ServiceCategoryController::class, 'getImportExample'])->name('service.categories.import.example');
Route::apiResource('/service-categories', v1\ServiceCategoryController::class)->names('service.categories');

Route::get('/clinics/{clinicId}/services', [v1\ServiceController::class, 'getClinicServices'])->name('get-clinic-services');
Route::post('/services/export', [v1\ServiceController::class, 'export'])->name('services.export');
Route::post('/services/import', [v1\ServiceController::class, 'import'])->name('services.import');
Route::get('/services/import-example', [v1\ServiceController::class, 'getImportExample'])->name('services.import.example');
Route::apiResource('/services', v1\ServiceController::class)->names('services');

Route::get('appointment-logs/{appointmentLogId}', [v1\AppointmentLogController::class, 'show'])->name('appointment.log.show');
Route::get('appointments/{appointmentId}/logs', [v1\AppointmentLogController::class, 'getAppointmentLogs'])->name('appointments.logs');

Route::post('/medicines/export', [v1\MedicineController::class, 'export'])->name('medicines.export');
Route::post('/medicines/import', [v1\MedicineController::class, 'import'])->name('medicines.import');
Route::get('/medicines/get-import-example', [v1\MedicineController::class, 'getImportExample'])->name('medicines.get.example');
Route::apiResource('/medicines', v1\MedicineController::class)->names('medicines');

Route::get('/transactions/balance', [v1\TransactionController::class, 'balance'])->name('transactions.balance');
Route::apiResource('/transactions', v1\TransactionController::class)->names('transactions');

Route::get('/statistics/index', [v1\StatisticsController::class, 'adminStatistics'])->name('statistics.index');

Route::post('/holidays/export', [v1\HolidayController::class, 'export'])->name('holidays.export');
Route::post('/holidays/import', [v1\HolidayController::class, 'import'])->name('holidays.import');
Route::get('/holidays/get-import-example', [v1\HolidayController::class, 'getImportExample'])->name('holidays.get.example');
Route::get('/holidays/active', [v1\HolidayController::class, 'activeHolidays'])->name('holidays.active');
Route::apiResource('/holidays', v1\HolidayController::class)->names('holidays');

Route::post('/media/customers/attachments', [v1\MediaController::class, 'addCustomerAttachment'])->name('media.customers.attachments.store');
Route::delete('/media/{mediaId}', [v1\MediaController::class, 'delete'])->name('media.delete');

Route::post('appointments/export', [v1\AppointmentController::class, 'export'])->name('appointments.export');
Route::put('appointments/change-status', [v1\AppointmentController::class, 'changeAppointmentStatus'])->name('appointments.change.status');
Route::get('/clinics/{clinicId}/appointments', [v1\AppointmentController::class, 'getByClinic'])->name('clinics.appointments');
Route::get('/customers/{customerId}/appointments', [v1\AppointmentController::class, 'getByCustomer'])->name('clinics.appointments');
Route::apiResource('appointments', v1\AppointmentController::class)->except(['destroy'])->names('appointments');

Route::post('clinics/available-appointments-times', [v1\AvailableAppointmentTimeController::class, 'get'])->name('clinics.available.appointments.time');

Route::get('/customers/{customerId}/prescriptions', [v1\PrescriptionController::class, 'getCustomerPrescriptions'])->name('customers.prescriptions');
Route::get('/prescriptions/{prescriptionId}', [v1\PrescriptionController::class, 'show'])->name('prescriptions.show');

Route::post('users/{userId}/attendances', [v1\AttendanceLogController::class, 'editOrCreate'])->name('users.attendance.edit');
Route::get('users/attendances', [v1\UserController::class, 'allWithAttendanceByDate'])->name('users.attendance.index');
Route::get('/attendances/import-example', [v1\AttendanceLogController::class, 'getImportExample'])->name('attendances.import.example');
Route::post('/attendances/import', [v1\AttendanceLogController::class, 'import'])->name('attendances.import');
Route::get('/attendances/export', [v1\AttendanceLogController::class, 'export'])->name('attendances.export');

Route::post('/users/secretaries', [v1\UserController::class, 'addSecretary'])->name('users.secretaries.store');
Route::get('/users/secretaries', [v1\UserController::class, 'secretaries'])->name('users.secretaries');
Route::apiResource('/users', v1\UserController::class)->except(['store', 'index'])->names('users');

Route::apiResource('/formulas', v1\FormulaController::class)->names('formulas');

Route::apiResource('formula-variables', v1\FormulaVariableController::class)
    ->except([
        'store',
        'update',
        'destroy'
    ])->names('formula.variables');
