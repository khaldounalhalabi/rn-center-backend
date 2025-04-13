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
        Route::post('/login', 'login')->name("login");
        Route::post('/password-reset-request', 'passwordResetRequest')->name("reset.password.request");
        Route::post('/check-reset-password-code', 'checkPasswordResetCode')->name("check.reset.password.code");
        Route::post('/reset-password', 'passwordReset')->name("password.reset");
        Route::post('/verify', 'verifyUser')->name("verify");
        Route::post('/resend-verification-code', 'resendVerificationCode')->name("resend.verification.code");
        Route::post('/resend-verification-code', 'resendVerificationCode')->name("resend.verification.code");
        Route::post('/register', 'register')->name("register");
    });

Route::prefix('/admin')
    ->controller(AdminAuthController::class)
    ->name('admin.')
    ->group(function () {
        Route::post('/login', 'login')->name("login");
        Route::post('/password-reset-request', 'passwordResetRequest')->name("reset-password-request");
        Route::post('/check-reset-password-code', 'checkPasswordResetCode')->name("check-reset-password-code");
        Route::post('/reset-password', 'passwordReset')->name("password-reset");
        Route::post('/verify', 'verifyUser')->name("verify");
        Route::post('/resend-verification-code', 'resendVerificationCode')->name("resend.verification.code");
    });


Route::prefix('doctor')
    ->controller(DoctorAuthController::class)
    ->name('doctor.')
    ->group(function () {
        Route::post('/login', 'login')->name("login");
        Route::post('/password-reset-request', 'passwordResetRequest')->name("reset-password-request");
        Route::post('/check-reset-password-code', 'checkPasswordResetCode')->name("check-reset-password-code");
        Route::post('/reset-password', 'passwordReset')->name("password-reset");
        Route::post('/verify', 'verifyUser')->name("verify");
        Route::post('/resend-verification-code', 'resendVerificationCode')->name("resend.verification.code");
    });

Route::prefix('secretary')
    ->controller(v1\SecretaryAuthController::class)
    ->name('doctor.')
    ->group(function () {
        Route::post('/login', 'login')->name("login");
        Route::post('/password-reset-request', 'passwordResetRequest')->name("reset-password-request");
        Route::post('/check-reset-password-code', 'checkPasswordResetCode')->name("check-reset-password-code");
        Route::post('/reset-password', 'passwordReset')->name("password-reset");
        Route::post('/verify', 'verifyUser')->name("verify");
        Route::post('/resend-verification-code', 'resendVerificationCode')->name("resend.verification.code");
    });

Route::get('/specialities', [v1\SpecialityController::class, 'getOrderedByClinicsCount'])->name('speciality.index');
Route::get('/service-categories', [v1\ServiceCategoryController::class, 'index'])->name('service.category.index');

Route::get('/clinics/{clinicId}', [v1\ClinicController::class, 'show'])->name('clinics.show');

Route::get('/check-role', [v1\BaseAuthController::class, 'checkRole'])->name('check-role');

Route::delete('/media/{mediaId}', [v1\MediaController::class, 'delete'])->name('media.delete');

Route::get('/statistics', [v1\StatisticsController::class, 'landingPage'])->name('statistics.landing.page');


Route::get('appointments/{code}/get-by-code', [v1\AppointmentController::class, 'getByCode'])->name('appointments.by.code');
