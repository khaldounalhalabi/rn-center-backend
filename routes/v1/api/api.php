<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\v1;

use Illuminate\Support\Facades\Route;

//add-your-routes-here
Route::apiResource("/users", v1\UserController::class)->names("api.users") ;
