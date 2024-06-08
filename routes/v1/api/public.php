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
        Route::post('/login', 'login')->name("login");
        Route::post('/password-reset-request', 'passwordResetRequest')->name("reset-password-request");
        Route::post('/check-reset-password-code', 'checkPasswordResetCode')->name("check-reset-password-code");
        Route::post('/reset-password', 'passwordReset')->name("password-reset");
        Route::post('/verify-email', 'verifyCustomerEmail')->name('verify-email');
        Route::post('/request-verification-code', 'requestVerificationCode')->name('request-verification-code');
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
Route::get('/hospitals' , [v1\HospitalController::class , 'index'])->name('hospital.index');
Route::get('/specialities' , [v1\SpecialityController::class , 'index'])->name('speciality.index');
Route::get('/subscriptions' , [v1\SubscriptionController::class , 'index'])->name('subscription.index');

Route::get('check-role', [v1\BaseAuthController::class, 'checkRole'])->name('check-role');
