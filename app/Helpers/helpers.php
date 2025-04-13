<?php

use App\Models\Clinic;
use App\Models\Customer;
use App\Models\User;
use App\Modules\ApiResponse;
use Illuminate\Contracts\Auth\Authenticatable;

if (!function_exists('rest')) {
    function rest()
    {
        return ApiResponse::create();
    }
}

if (!function_exists('user')) {
    function user(): User|Authenticatable|null
    {
        return auth()->user();
    }
}

if (!function_exists('clinic')) {
    function clinic(): ?Clinic
    {
        return auth()->user()?->clinic;
    }
}

if (!function_exists('isDoctor')) {
    function isDoctor(): bool
    {
        return (bool)auth()->user()?->isDoctor();
    }
}

if (!function_exists('isCustomer')) {
    function isCustomer(): bool
    {
        return (bool)auth()->user()?->isCustomer();
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return (bool)auth()->user()?->isAdmin();
    }
}

if (!function_exists('isSecretary')) {
    function isSecretary(): bool
    {
        return (bool)auth()->user()?->isSecretary();
    }
}

if (!function_exists('customer')) {
    function customer(): ?Customer
    {
        return auth()->user()?->customer;
    }
}
