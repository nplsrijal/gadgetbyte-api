<?php


use App\Http\Controllers\Api\V1\UserTypeController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\LoginController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('v1/login', [LoginController::class, 'login']);


Route::prefix('v1')->middleware(['client','verify_header'])->group(function () {
    // Route will be here

   
    Route::apiResource('usertypes', UserTypeController::class);
    Route::apiResource('users', UserController::class);
    Route::post('logout', [LoginController::class, 'logout']);


   

});
