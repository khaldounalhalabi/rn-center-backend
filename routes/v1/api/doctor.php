<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

Route::post('/refresh', [v1\DoctorAuthController::class, 'refresh'])->name("refresh.token");
Route::post('/logout', [v1\DoctorAuthController::class, 'logout'])->name("logout");
Route::post('/update-user-data', [v1\DoctorAuthController::class, 'updateUserDetails'])->name("update.user.data");
Route::get('/me', [v1\DoctorAuthController::class, 'userDetails'])->name('user.details');

Route::name('appointments.')
    ->group(function () {
        Route::get('/appointments', [v1\AppointmentController::class, 'index'])->name('index');
        Route::get('/appointments/{appointmentId}', [v1\AppointmentController::class, 'show'])->name('show');
        Route::put('/appointments/{appointmentId}', [v1\AppointmentController::class, 'update'])->name('update');
        Route::get('/customers/{customerId}/appointments', [v1\AppointmentController::class, 'getByCustomer'])->name('get.by.customer');
    });

Route::resource('prescriptions', v1\PrescriptionController::class)->except(['index'])->names('prescriptions');

Route::post('available-appointments-times', [v1\AvailableAppointmentTimeController::class, 'get'])->name('available.appointments.time');

Route::get('/medicines', [v1\MedicineController::class, 'index'])->name('medicine.index');
Route::get('/medicines/{medicineId}', [v1\MedicineController::class, 'show'])->name('medicine.show');
Route::post('/medicines', [v1\MedicineController::class, 'store'])->name('medicine.store');

Route::get('/customers/{customerId}/medical-records', [v1\MedicalRecordController::class, 'getByCustomer'])->name('customers.medical.records');
Route::apiResource('/medical-records', v1\MedicalRecordController::class)->except(['index'])->names('medical.records');

Route::get('/customers/{customerId}/pdf-report', [v1\CustomerController::class, 'pdfReport'])->name('customers.pdf.report');
Route::get('/customers', [v1\CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers/{customerId}', [v1\CustomerController::class, 'show'])->name('customers.show');
Route::put('/customers/{customerId}', [v1\CustomerController::class, 'update'])->name('customers.update');
Route::get('/customers/{customerId}/prescriptions', [v1\PrescriptionController::class, 'getByCustomer'])->name('customers.prescriptions');

Route::get('services', [v1\ServiceController::class, 'index'])->name('services.index');
Route::get('services/{serviceId}', [v1\ServiceController::class, 'show'])->name('services.show');
Route::put('services/{serviceId}', [v1\ServiceController::class, 'update'])->name('services.update');

Route::get('service-categories', [v1\ServiceCategoryController::class, 'index'])->name('services.categories.index');

Route::get('/holidays/active', [v1\HolidayController::class, 'activeHolidays'])->name('holidays.active');
Route::get('holidays', [v1\HolidayController::class, 'index'])->name('holidays.index');

Route::delete('/media/{mediaId}', [v1\MediaController::class, 'delete'])->name('media.delete');

Route::get('/attendances', [v1\AttendanceLogController::class, 'myAttendance'])->name('attendances.index');
Route::get('/attendances/checkin', [v1\AttendanceLogController::class, 'checkin'])->name('attendances.checkin');
Route::get('/attendances/checkout', [v1\AttendanceLogController::class, 'checkout'])->name('attendances.checkout');
Route::get('/attendances/latest', [v1\AttendanceLogController::class, 'latestLog'])->name('attendances.latest');
Route::get('/attendances/export', [v1\AttendanceLogController::class, 'export'])->name('attendances.export');
Route::get('/attendances/statistics', [v1\AttendanceLogController::class, 'myStatistics'])->name('attendances.statistics');

Route::get('/schedule', [v1\ScheduleController::class, 'mySchedule'])->name('schedule.mine');

Route::post('/payslips/{payslipId}/toggle-status', [v1\PayslipController::class, 'toggleStatus'])->name('payslips.toggle.status');
Route::get('/payslips/{payslipId}/pdf', [v1\PayslipController::class, 'toPdf'])->name('payslips.pdf');
Route::get('/payslips', [v1\PayslipController::class, 'mine'])->name('payslips.mine');
Route::get('/payslips/{payslipId}', [v1\PayslipController::class, 'show'])->name('payslips.show');

Route::get('/vacations/active', [v1\VacationController::class, 'myActiveVacations'])->name('vacations.active');
Route::apiResource('vacations', v1\VacationController::class)->except(['update'])->names('vacations');
