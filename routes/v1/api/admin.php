<?php

use App\Http\Controllers\API\v1;
use App\Http\Controllers\API\v1\AppointmentController;
use Illuminate\Support\Facades\Route;

//add-your-routes-here

Route::controller(v1\AdminAuthController::class)
    ->group(function () {
        Route::post('/refresh', 'refresh')->name('refresh-token');
        Route::post('/logout', 'logout')->name('logout');
        Route::post('/update-user-data', 'updateUserDetails')->name('update-user-data');
        Route::get('/me', 'userDetails')->name('me');
        Route::post('/fcm/store-token', 'storeFcmToken')->name('fcm.storeToken');
        Route::get('/fcm/get-token', 'getUserFcmToken')->name('fcm.getToken');
    });

Route::controller(v1\NotificationController::class)
    ->prefix('notifications')
    ->name('notifications.')
    ->group(function () {
        Route::get('/', 'getUserNotification')->name('index');
        Route::get('/unread/count', 'unreadCount')->name('unread.count');
        Route::get('/{notificationId}/mark-as-read', 'markAsRead')->name('mark.as.read');
    });
Route::controller(v1\UserController::class)
    ->name('users.')
    ->group(function () {
        Route::delete('/{userId}/toggle-archive', 'toggleArchive')->name('toggle.archive');
        Route::get('/{userId}/toggle-block', 'toggleBlock')->name('block.toggle');
    });
Route::apiResource('/users', v1\UserController::class)->except(['store'])->names('users');

Route::controller(v1\ClinicController::class)
    ->group(function () {
        Route::get('/system-offers/{systemOfferId}/clinics', 'getBySystemOffer')->name('system.offers.clinics');
        Route::get('/clinics/{clinicId}/toggle-status', 'toggleClinicStatus')->name('clinics.status.toggle');
        Route::get('/subscriptions/{subscriptionId}/clinics', 'getBySubscription')->name('subscription.clinics');
        Route::get('/clinics/{clinicId}/available-times', 'getClinicAvailableTimes')->name('clinics.available.times');
    });
Route::apiResource('/clinics', v1\ClinicController::class)->names('clinics');


Route::controller(v1\ScheduleController::class)
    ->group(function () {
        Route::get('/clinics/{clinicId}/schedules', 'clinicSchedules')->name('clinics.schedules');
        Route::delete('clinics/{clinicId}/schedules', 'deleteAllClinicSchedules')->name('clinics.schedules.delete');
        Route::post('schedules', 'storeUpdateSchedules')->name('schedules.storeOrUpdate');
    });

Route::controller(v1\CustomerController::class)
    ->group(function () {
        Route::get('/customers/recent', 'getRecent')->name('customers.recent');
        Route::get('/clinics/{clinicId}/customers', 'getByClinic')->name('clinics.customers');
    });
Route::apiResource('/customers', v1\CustomerController::class)->names('customers');


Route::get('/hospitals/{hospitalId}/toggle-status', [v1\HospitalController::class, 'toggleHospitalStatus'])->name('hospitals.toggle.status');
Route::apiResource('/hospitals', v1\HospitalController::class)->names('hospitals');

Route::apiResource('/available-departments', v1\AvailableDepartmentController::class)->names('available.departments');

Route::apiResource('/specialities', v1\SpecialityController::class)->names('specialities');

Route::apiResource('/clinic-holidays', v1\ClinicHolidayController::class)->names('clinic.holidays');

Route::apiResource('/service-categories', v1\ServiceCategoryController::class)->names('service.categories');

Route::get('/clinics/{clinicId}/services', [v1\ServiceController::class, 'getClinicServices'])->name('get-clinic-services');
Route::apiResource('/services', v1\ServiceController::class)->names('services');

Route::controller(v1\AppointmentLogController::class)
    ->name('appointments.logs.')
    ->group(function () {
        Route::get('appointments/{appointmentId}/logs', 'getAppointmentLogs')->name('index');
        Route::get('appointment-logs/{appointmentLogId}', 'show')->name('show');
    });

Route::controller(AppointmentController::class)
    ->group(function () {
        Route::get('/clinics/{clinicId}/appointments', 'getClinicAppointments')->name('clinics.appointments');
        Route::put('appointments/{appointmentId}/update-date', 'updateAppointmentDate')->name('appointments.update.date');
        Route::post('appointments/{appointmentId}/toggle-status', 'toggleAppointmentStatus')->name('appointments.status.toggle');
        Route::get('customers/{customerId}/clinics/{clinicId}/last-appointment', 'getCustomerLastAppointment')->name('customers.clinics.last-appointment');
    });
Route::apiResource('/appointments', v1\AppointmentController::class)
    ->except(['destroy'])->names('appointments');

Route::apiResource('/medicines', v1\MedicineController::class)->names('medicines');

Route::controller(v1\PrescriptionController::class)
    ->group(function () {
        Route::get('appointments/{appointmentId}/prescriptions/', 'getAppointmentPrescriptions')->name('appointments.prescriptions');
        Route::delete('/prescriptions/medicine-data/{medicineDataId}', 'removeMedicine')->name('prescription.medicine.remove');
    });
Route::apiResource('prescriptions', v1\PrescriptionController::class)->names('prescriptions');

Route::apiResource('/blocked-items', v1\BlockedItemController::class)->names('blocked.items');

Route::apiResource('/subscriptions', v1\SubscriptionController::class)
    ->except(['update'])
    ->names('subscriptions');

Route::post('/enquiries/{enquiryId}/reply', [v1\EnquiryController::class, 'reply'])->name('enquiries.reply');
Route::apiResource('/enquiries', v1\EnquiryController::class)
    ->except(['update', 'store', 'destroy'])
    ->names('enquiries');

Route::controller(v1\ClinicSubscriptionController::class)
    ->prefix('clinics/{clinicId}')
    ->name('clinics.clinic.subscriptions.')
    ->group(function () {
        Route::get('/clinic-subscriptions/current/pay', 'makeItPaid')->name('current.pay');
        Route::get('/subscriptions', 'getByClinic')->name('index');
    });
Route::apiResource('/clinic-subscriptions', v1\ClinicSubscriptionController::class)
    ->except(['index'])
    ->names('clinic.subscriptions');

Route::apiResource('offers', v1\OfferController::class)->names('offers');

Route::get('customers/{customerId}/patient-profiles', [v1\PatientProfileController::class, 'getCustomerPatientProfiles']);
Route::apiResource('/patient-profiles', v1\PatientProfileController::class)->names('patient.profiles');

Route::get('transactions/summary', [v1\TransactionController::class, 'summary'])->name('transaction.summary');
Route::apiResource('/transactions', v1\TransactionController::class)->names('transactions');

Route::apiResource('/blood-donation-requests', v1\BloodDonationRequestController::class)->names('blood.donation.requests');

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

Route::get('/settings/by-label/{label}', [v1\SettingController::class, 'getByLabel'])->name('settings.label');
Route::apiResource('settings', v1\SettingController::class)
    ->only(['index', 'update', 'show'])
    ->names('settings');

Route::get('clinics/{clinicId}/reviews', [v1\ReviewController::class, 'getByClinic'])->name('clinics.reviews');

Route::get('/statistics/index', [v1\StatisticsController::class, 'adminStatistics'])->name('statistics.index');

Route::apiResource('clinic-join-requests', v1\ClinicJoinRequestController::class)
    ->except([
        'store',
        'update',
    ])->names('clinic.join.requests');
