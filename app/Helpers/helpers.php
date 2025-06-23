<?php

use App\Enums\PermissionEnum;
use App\Models\Clinic;
use App\Models\Customer;
use App\Models\User;
use App\Modules\ApiResponse;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;

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

if (!function_exists('can')) {
    function can(PermissionEnum $permission): bool
    {
        return (boolean)user()?->hasPermissionTo($permission->value);
    }
}

if (!function_exists('fakeImage')) {
    function fakeImage(HasMedia $model, bool $multiple = false): void
    {
        if (!$multiple) {
            $num = fake()->numberBetween(1, 4);
            $model
                ->addMedia(new UploadedFile(storage_path("app/required/img$num.png"), "img$num.png"))
                ->preservingOriginal()
                ->toMediaCollection();
        } else {
            for ($i = 1; $i <= 4; $i++) {
                $model
                    ->addMedia(new UploadedFile(storage_path("app/required/img$i.png"), "img$i.png"))
                    ->preservingOriginal()
                    ->toMediaCollection();
            }
        }
    }
}


