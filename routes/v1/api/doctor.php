<?php

use App\Http\Controllers\API\v1;
use App\Http\Controllers\API\v1\ClinicTransactionController;
use App\Models\Clinic;
use App\Models\ClinicEmployee;
use App\Models\ClinicHoliday;
use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Offer;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;

Route::post('/refresh', [v1\DoctorAuthController::class, 'refresh'])->name("refresh.token");
Route::post('/logout', [v1\DoctorAuthController::class, 'logout'])->name("logout");
Route::post('/update-user-data', [v1\DoctorAuthController::class, 'updateUserDetails'])->name("update.user.data");
Route::get('/me', [v1\DoctorAuthController::class, 'userDetails'])->name('user.details');
Route::post('fcm/store-token', [v1\AdminAuthController::class, 'storeFcmToken'])->name('fcm.storeToken');
Route::get('fcm/get-token', [v1\AdminAuthController::class, 'getUserFcmToken'])->name('fcm.getToken');

Route::get('/contract/agree', [v1\ClinicController::class, 'agreeOnContract'])
    ->withoutMiddleware(['contract'])->name('contract.agree');

Route::get('notifications', [v1\NotificationController::class, 'getUserNotification'])->name('notifications');
Route::get('notifications/unread/count', [v1\NotificationController::class, 'unreadCount'])->name('notification.unread.count');
Route::get('notifications/{notificationId}/mark-as-read', [v1\NotificationController::class, 'markAsRead'])->name('notifications');

Route::get('available-times', [v1\ClinicController::class, 'getCurrentClinicAvailableTime'])->name('available-times');
Route::get('clinic-subscriptions', [v1\ClinicSubscriptionController::class, 'getCurrentClinicSubscriptions'])->name('clinic.subscriptions');

Route::put('/clinic/update', [v1\ClinicController::class, 'updateDoctorClinic'])
    ->middleware([
        'staff_can:edit-clinic-profile,' . Clinic::class,
    ])->name('clinic.update');
Route::get('/clinic', [v1\ClinicController::class, 'showDoctorClinic'])
    ->name('clinic.show');

Route::post('/schedules', [v1\ScheduleController::class, 'storeUpdateSchedules'])->middleware([
    'staff_can:manage-schedules,' . Schedule::class,
])->name('schedules.store');
Route::get('/schedules', [v1\ScheduleController::class, 'getCurrentClinicSchedules'])
    ->middleware([
        'staff_can:manage-schedules,' . Schedule::class,
    ])->name('schedules.show');

Route::get('clinic-holidays', [v1\ClinicHolidayController::class, 'getCurrentClinicHolidays'])->name('holidays');
Route::apiResource('clinic-holidays', v1\ClinicHolidayController::class)
    ->middleware([
        'staff_can:manage-holidays,' . ClinicHoliday::class,
    ])->except(['index'])->names('holidays');

Route::get('services/names', [v1\ServiceController::class, 'getClinicServicesNames'])->name('services.names');
Route::get('services', [v1\ServiceController::class, 'index'])->name('service.index');
Route::apiResource('services', v1\ServiceController::class)
    ->middleware([
        'staff_can:manage-services,' . Service::class,
    ])->except(['index'])->names('services');

Route::get('offers', [v1\OfferController::class, 'index'])->name('offer.index');
Route::apiResource('offers', v1\OfferController::class)
    ->except(['index'])
    ->middleware([
        'staff_can:manage-offers,' . Offer::class,
    ])->names('offers');

Route::get('customers', [v1\CustomerController::class, 'getDoctorCustomers'])->name('customers.index');
Route::get('customers/{customerId}', [v1\CustomerController::class, 'show'])
    ->middleware([
        'staff_can:manage-patients,' . Customer::class,
    ])->name('customers.show');
Route::post('customers', [v1\CustomerController::class, 'doctorAddCustomer'])
    ->middleware([
        'staff_can:manage-patients,' . Customer::class,
    ])->name('customers.store');
Route::put('customers/{customerId}', [v1\CustomerController::class, 'doctorUpdateCustomer'])
    ->middleware([
        'staff_can:manage-patients,' . Customer::class,
    ])->name('customers.update');
Route::delete('customers/{customerId}', [v1\CustomerController::class, 'doctorDeleteCustomer'])
    ->middleware([
        'staff_can:manage-patients,' . Customer::class,
    ])->name('customers.delete');

Route::get('/appointments/all/group-by-month', [v1\AppointmentController::class, 'getAppointmentsCountInMonth'])->name('appointments.all.group.by.month');
Route::get('/appointments/completed/group-by-month', [v1\AppointmentController::class, 'getAppointmentsCompletedCountInMonth'])->name('appointments.completed.group.by.month');
Route::get('/appointments/recent', [v1\AppointmentController::class, 'recentAppointments'])->name('appointments.recent');

Route::get('/appointments/today', [v1\AppointmentController::class, 'todayAppointments'])->name('appointments.today');
Route::get('/appointments/{appointmentId}/prescriptions', [v1\PrescriptionController::class, 'getAppointmentPrescriptions'])->name('appointments.prescriptions');
Route::delete('/prescriptions/medicine-data/{medicineDataId}', [v1\PrescriptionController::class, 'removeMedicine'])->name('prescription.medicine.remove');
Route::get('/customers/{customerId}/prescriptions', [v1\PrescriptionController::class, 'getCustomerPrescriptions'])->name('customer.prescriptions');
Route::apiResource('/prescriptions', v1\PrescriptionController::class)->except(['index'])->names('prescriptions');

Route::get('medicines', [v1\MedicineController::class, 'index'])->name('medicines.index');
Route::apiResource('medicines', v1\MedicineController::class)
    ->except(['index'])
    ->middleware([
        'staff_can:manage-medicines,' . Medicine::class,
    ])->names('medicines');

Route::put('clinic-employees/{clinicEmployeeId}/update-permissions', [v1\ClinicEmployeeController::class, 'updateEmployeePermissions'])
    ->middleware([
        'staff_can:manage-employees,' . ClinicEmployee::class,
    ])->name('clinic-employee.permissions-update');

Route::get('clinic-employees', [v1\ClinicEmployeeController::class, 'index'])
    ->name('clinic.employees.index');

Route::apiResource('/clinic-employees', v1\ClinicEmployeeController::class)
    ->except(['index'])
    ->middleware([
        'staff_can:manage-employees,' . ClinicEmployee::class,
    ])->names('clinic.employees');

Route::prefix('clinic-transactions')
    ->name('clinic.transactions.')
    ->middleware([
        'staff_can:accountant-management,' . Transaction::class,
    ])->controller(ClinicTransactionController::class)
    ->group(function () {
        Route::get('/all', 'all')->name('all');
        Route::get('/summary', 'summary')->name('summary');
        Route::get('/export', 'export')->name('export');
    });
Route::apiResource('clinic-transactions', v1\ClinicTransactionController::class)
    ->middleware([
        'staff_can:accountant-management,' . Transaction::class,
    ])->names('clinic.transactions');

Route::get('/appointments/all', [v1\AppointmentController::class, 'all'])->name('appointments.all');
Route::get('/customers/{customerId}/appointments', [v1\AppointmentController::class, 'getByCustomer'])->name('customers.appointments');
Route::get('/appointments/export', [v1\AppointmentController::class, 'export'])->name('appointments.export');
Route::put('/appointments/{appointmentId}/update-date', [v1\AppointmentController::class, 'updateAppointmentDate'])->name('appointments.update.date');
Route::post('/appointments/{appointmentId}/toggle-status', [v1\AppointmentController::class, 'toggleAppointmentStatus'])->name('appointments.status.toggle');
Route::get('/appointment-logs/{appointmentLogId}', [v1\AppointmentLogController::class, 'show'])->name('appointment.log.show');
Route::get('/appointments/{appointmentId}/logs', [v1\AppointmentLogController::class, 'getAppointmentLogs'])->name('appointments.logs');
Route::get('/customers/{customerId}/last-appointment', [v1\AppointmentController::class, 'getCustomerLastAppointment'])->name('customers.clinics.last-appointment');
Route::apiResource('/appointments', v1\AppointmentController::class)
    ->except(['destroy'])->names('appointments');

Route::get('/statistics/index-page', [v1\StatisticsController::class, 'doctorIndexStatistics'])->name('doctor.index.statistics');
