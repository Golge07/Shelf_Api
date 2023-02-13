<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [App\Http\Controllers\UserController::class, 'login']);
Route::post('/register', [App\Http\Controllers\UserController::class, 'register']);
Route::get('/user/verify/email/{token}', [App\Http\Controllers\UserController::class, 'verify']);


/* User Post Requests*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user/get', [App\Http\Controllers\UserController::class, 'get_user_by_token']);
    Route::post('/user/all', [App\Http\Controllers\UserController::class, 'get_users_by_permission']);
    Route::post('/user/logout', [App\Http\Controllers\UserController::class, 'logout']);
    Route::post('/user/update', [App\Http\Controllers\UserController::class, 'update_user']);
    Route::post('/user/delete', [App\Http\Controllers\UserController::class, 'delete_user']);
    Route::post('/user/search', [App\Http\Controllers\UserController::class, 'search_user']);
    Route::post('/user/verify/send', [App\Http\Controllers\UserController::class, 'verify_send']);
});

/* Shelf Post Requests*/
Route::middleware('auth:sanctum')->group(function () {
});