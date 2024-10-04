<?php

use App\Http\Controllers\API\v1;
use App\Http\Controllers\API\v1\AdminAuthController;
use App\Http\Controllers\API\v1\CustomerAuthController;
use App\Http\Controllers\API\v1\DoctorAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('/customer')
    ->controller(CustomerAuthController::class)
    ->name('customer.')
    ->group(function () {
        Route::post('/register', 'register')->name("register");
        Route::post('/login', 'loginByPhone')->name("login");
        Route::post('/password-reset-request', 'requestResetPasswordCodeByPhone')->name("reset-password-request");
        Route::post('/reset-password', 'passwordResetByPhone')->name("password-reset");
        Route::post('/verify-phone', 'verifyCustomerPhone')->name('verify-phone');
        Route::post('/request-verification-code', 'requestVerificationCodeByPhone')->name('request-verification-code');
        Route::post('/validate-reset-code', 'validateResetCode')->name('validate.reset.code');
    });

Route::prefix('/admin')
    ->controller(AdminAuthController::class)
    ->name('admin.')
    ->group(function () {
        Route::post('/login', 'login')->name("login");
        Route::post('/password-reset-request', 'passwordResetRequest')->name("reset-password-request");
        Route::post('/check-reset-password-code', 'checkPasswordResetCode')->name("check-reset-password-code");
        Route::post('/reset-password', 'passwordReset')->name("password-reset");
    });


Route::prefix('doctor')
    ->controller(DoctorAuthController::class)
    ->name('doctor.')
    ->group(function () {
        Route::post('/login', 'login')->name("login");
        Route::post('/password-reset-request', 'passwordResetRequest')->name("reset-password-request");
        Route::post('/check-reset-password-code', 'checkPasswordResetCode')->name("check-reset-password-code");
        Route::post('/reset-password', 'passwordReset')->name("password-reset");
    });

Route::get('/cities', [v1\CityController::class, 'index'])->name('cities.index');

Route::get('/hospitals', [v1\HospitalController::class, 'index'])->name('hospital.index');
Route::get('/hospitals/{hospitalId}', [v1\HospitalController::class, 'show'])->name('hospital.show');

Route::get('/specialities', [v1\SpecialityController::class, 'getOrderedByClinicsCount'])->name('speciality.index');
Route::get('/subscriptions', [v1\SubscriptionController::class, 'index'])->name('subscription.index');
Route::get('/service-categories', [v1\ServiceCategoryController::class, 'index'])->name('service.category.index');

Route::get('/clinics/{clinicId}/system-offers', [v1\SystemOfferController::class, 'getByClinic'])->name('clinics.system.offers');
Route::get('/system-offers', [v1\SystemOfferController::class, 'index'])->name('system.offers.index');
Route::get('/system-offers/{systemOfferId}', [v1\SystemOfferController::class, 'show'])->name('system.offers.show');

Route::get('/clinics/{clinicId}/offers', [v1\OfferController::class, 'getByClinic'])->name('clinics.offers');
Route::get('/offers/{offerId}', [v1\OfferController::class, 'show'])->name('offers.show');

Route::get('/specialities/{specialityId}/clinics' , [v1\ClinicController::class , 'getOnlineBySpeciality'])->name('specialities.clinics');
Route::get('/clinics', [v1\ClinicController::class, 'featured'])->name('clinics.featured');
Route::get('/clinics/{clinicId}', [v1\ClinicController::class, 'show'])->name('clinics.show');

Route::get('/check-role', [v1\BaseAuthController::class, 'checkRole'])->name('check-role');

Route::delete('/media/{mediaId}', [v1\MediaController::class, 'delete'])->name('media.delete');

Route::apiResource('blood-donations', v1\BloodDonationRequestController::class)
    ->except(['update', 'destroy'])
    ->names('blood.donations.request');

Route::get('/statistics', [v1\StatisticsController::class, 'landingPage'])->name('statistics.landing.page');

Route::post('clinic-join-requests', [v1\ClinicJoinRequestController::class, 'store'])->name('clinic.join.requests.store');

Route::get('/settings/by-label/{label}', [v1\SettingController::class, 'getByLabel'])->name('settings.label');


