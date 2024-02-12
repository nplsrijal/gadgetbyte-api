<?php


use App\Http\Controllers\Api\V1\UserTypeController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\PermissionController;
use App\Http\Controllers\Api\V1\UserPermissionController;
use App\Http\Controllers\Api\V1\PostCategoryController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\MediaController;

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


Route::prefix('v1')->middleware(['apiMiddleware','verify_header'])->group(function () {
    // Route will be here

   
    Route::apiResource('usertypes', UserTypeController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('menus', MenuController::class);
    Route::apiResource('menu-permissions', PermissionController::class);
    Route::apiResource('user-menus', UserPermissionController::class);
    Route::apiResource('post-categories', PostCategoryController::class);
    Route::apiResource('tags', TagController::class);
    Route::apiResource('medias', MediaController::class);
    Route::post('logout', [LoginController::class, 'logout']);


   

});
