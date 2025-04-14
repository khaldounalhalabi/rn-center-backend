<?php

use App\Http\Controllers\API\v1;
use Illuminate\Support\Facades\Route;

Route::post('/refresh', [v1\AdminAuthController::class, 'refresh'])->name("refresh.token");
Route::post('/logout', [v1\AdminAuthController::class, 'logout'])->name("logout");
Route::post('/update-user-data', [v1\AdminAuthController::class, 'updateUserDetails'])->name("update.user.data");
Route::get('/me', [v1\AdminAuthController::class, 'userDetails'])->name('user.details');
Route::post('/fcm/store-token', [v1\AdminAuthController::class, 'storeFcmToken'])->name('fcm.storeToken');
Route::get('/fcm/get-token', [v1\AdminAuthController::class, 'getUserFcmToken'])->name('fcm.getToken');

Route::get('notifications', [v1\NotificationController::class, 'getUserNotification'])->name('notifications');
Route::get('notifications/unread/count', [v1\NotificationController::class, 'unreadCount'])->name('notification.unread.count');
Route::get('/notifications/{notificationId}/mark-as-read', [v1\NotificationController::class, 'markAsRead'])->name('notifications');

Route::apiResource('/users', v1\UserController::class)->except(['store'])->names('users');

Route::apiResource('/clinics', v1\ClinicController::class)->names('clinics');

Route::controller(v1\ScheduleController::class)
    ->group(function () {
        Route::get('/clinics/{clinicId}/schedules', 'clinicSchedules')->name('clinics.schedules');
        Route::delete('clinics/{clinicId}/schedules', 'deleteAllClinicSchedules')->name('clinics.schedules.delete');
        Route::post('schedules', 'storeUpdateSchedules')->name('schedules.storeOrUpdate');
        Route::get('/clinics/{clinicId}/appointments', [v1\AppointmentController::class, 'getClinicAppointments'])->name('clinics.appointments');
        Route::get('/clinics/{clinicId}/available-times', [v1\ClinicController::class, 'getClinicAvailableTimes'])->name('clinic.get.clinic.available.times');
    });

Route::get('/customers/recent', [v1\CustomerController::class, 'getRecent'])->name('customers.recent');
Route::get('/clinics/{clinicId}/customers', [v1\CustomerController::class, 'getByClinic'])->name('clinics.customers');
Route::get('customers/{customerId}/patient-profiles', [v1\PatientProfileController::class, 'getCustomerPatientProfiles']);
Route::apiResource('/customers', v1\CustomerController::class)->names('customers');

Route::post('/specialities/export', [v1\SpecialityController::class, 'export'])->name('specialities.export');
Route::post('/specialities/import', [v1\SpecialityController::class, 'import'])->name('specialities.import');
Route::get('/specialities/import-example', [v1\SpecialityController::class, 'getImportExample'])->name('specialities.import.example');
Route::apiResource('/specialities', v1\SpecialityController::class)->names('specialities');

Route::apiResource('/clinic-holidays', v1\ClinicHolidayController::class)->names('clinic.holidays');


Route::post('/service-categories/export', [v1\ServiceCategoryController::class, 'export'])->name('service.categories.export');
Route::post('/service-categories/import', [v1\ServiceCategoryController::class, 'import'])->name('service.categories.import');
Route::get('/service-categories/import-example', [v1\ServiceCategoryController::class, 'getImportExample'])->name('service.categories.import.example');
Route::apiResource('/service-categories', v1\ServiceCategoryController::class)->names('service.categories');

Route::get('/clinics/{clinicId}/services', [v1\ServiceController::class, 'getClinicServices'])->name('get-clinic-services');
Route::post('/services/export', [v1\ServiceController::class, 'export'])->name('services.export');
Route::post('/services/import', [v1\ServiceController::class, 'import'])->name('services.import');
Route::get('/services/import-example', [v1\ServiceController::class, 'getImportExample'])->name('services.import.example');
Route::apiResource('/services', v1\ServiceController::class)->names('services');

Route::put('appointments/{appointmentId}/update-date', [v1\AppointmentController::class, 'updateAppointmentDate'])->name('appointments.update.date');
Route::post('appointments/{appointmentId}/toggle-status', [v1\AppointmentController::class, 'toggleAppointmentStatus'])->name('appointments.status.toggle');
Route::get('appointments/{appointmentId}/prescriptions/', [v1\PrescriptionController::class, 'getAppointmentPrescriptions'])->name('appointments.prescriptions');
Route::get('appointment-logs/{appointmentLogId}', [v1\AppointmentLogController::class, 'show'])->name('appointment.log.show');
Route::get('appointments/{appointmentId}/logs', [v1\AppointmentLogController::class, 'getAppointmentLogs'])->name('appointments.logs');
Route::get('customers/{customerId}/clinics/{clinicId}/last-appointment', [v1\AppointmentController::class, 'getCustomerLastAppointment'])->name('customers.clinics.last-appointment');
Route::apiResource('/appointments', v1\AppointmentController::class)
    ->except(['destroy'])->names('appointments');

Route::apiResource('/medicines', v1\MedicineController::class)->names('medicines');

Route::delete('/prescriptions/medicine-data/{medicineDataId}', [v1\PrescriptionController::class, 'removeMedicine'])->name('prescription.medicine.remove');
Route::apiResource('prescriptions', v1\PrescriptionController::class)->names('prescriptions');

Route::apiResource('/patient-profiles', v1\PatientProfileController::class)->names('patient.profiles');

Route::get('transactions/summary', [v1\TransactionController::class, 'summary'])->name('transaction.summary');
Route::apiResource('/transactions', v1\TransactionController::class)->names('transactions');

Route::get('/statistics/index', [v1\StatisticsController::class, 'adminStatistics'])->name('statistics.index');
