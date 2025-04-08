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

Route::get('system-offers/{systemOfferId}/clinics', [v1\ClinicController::class, 'getBySystemOffer'])->name('system.offers.clinics');
Route::get('/clinics/{clinicId}/toggle-status', [v1\ClinicController::class, 'toggleClinicStatus'])->name('clinic.status.toggle');
Route::get('/subscriptions/{subscriptionId}/clinics', [v1\ClinicController::class, 'getBySubscription'])->name('subscription.clinics');
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

Route::apiResource('/specialities', v1\SpecialityController::class)->names('specialities');

Route::apiResource('/addresses', v1\AddressController::class)->names('addresses');

Route::apiResource('/cities', v1\CityController::class)->names('cities');

Route::apiResource('/clinic-holidays', v1\ClinicHolidayController::class)->names('clinic.holidays');

Route::apiResource('/service-categories', v1\ServiceCategoryController::class)->names('service.categories');

Route::get('/clinics/{clinicId}/services', [v1\ServiceController::class, 'getClinicServices'])->name('get-clinic-services');
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

Route::apiResource('/subscriptions', v1\SubscriptionController::class)
    ->except(['update'])
    ->names('subscriptions');

Route::get('/clinic-subscriptions/{clinicSubscriptionId}/pay', [v1\ClinicSubscriptionController::class, 'collectSubscription'])->name('clinic.subscriptions.pay');
Route::get('/clinics/{clinicId}/clinic-subscriptions/current/pay', [v1\ClinicSubscriptionController::class, 'makeItPaid'])->name('clinics.clinic.subscriptions.current.pay');
Route::get('clinics/{clinicId}/subscriptions', [v1\ClinicSubscriptionController::class, 'getByClinic'])->name('clinics.subscriptions');
Route::apiResource('/clinic-subscriptions', v1\ClinicSubscriptionController::class)
    ->except(['index'])
    ->names('clinic.subscriptions');

Route::apiResource('offers', v1\OfferController::class)->names('offers');

Route::apiResource('/patient-profiles', v1\PatientProfileController::class)->names('patient.profiles');

Route::get('transactions/summary', [v1\TransactionController::class, 'summary'])->name('transaction.summary');
Route::apiResource('/transactions', v1\TransactionController::class)->names('transactions');

Route::apiResource('/system-offers', v1\SystemOfferController::class)->names('system.offers');

Route::controller(v1\AppointmentDeductionController::class)
    ->group(function () {
        Route::prefix('/clinics/{clinicId}/appointment-deductions')
            ->name('clinics.appointment.deductions.')
            ->group(function () {
                Route::get('/current-month/total', 'getDeductionsTotalForThisMonth')->name('current.month.total');
                Route::get('/current-month/collect', 'collectForThisMonth')->name('current.month.collect');
                Route::get('/summary', 'getSummaryByClinicId')->name('summary');
                Route::get('/', 'getByClinic')->name('index');
            });

        Route::prefix('appointment-deductions')
            ->name('appointment.deductions.')
            ->group(function () {
                Route::get('/earnings', 'deductionsSummedByMonth')->name('earnings');
                Route::get('/all', 'all')->name('all');
                Route::post('/bulk/toggle-status', 'bulkToggleStatus')->name('bulk.toggle.status');
                Route::get('/summary', 'adminSummary')->name('summary');
                Route::get('/export', 'export')->name('export');
                Route::get('/{appointmentDeductionId}/toggle-status', 'toggleStatus')->name('status.toggle');
            });
    });
Route::apiResource('/appointment-deductions', v1\AppointmentDeductionController::class)
    ->only(['index', 'show'])->names('appointment.deductions');


Route::get('/statistics/index', [v1\StatisticsController::class, 'adminStatistics'])->name('statistics.index');
