<?php

use App\Http\Controllers\VerifyController;
use App\Mail\SendVerifyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
Route::get('/user/verify/email/{token}', [App\Http\Controllers\VerifyController::class, 'verify_mail']);
Route::post('test',function(Request $req){
    $verCtrl = new VerifyController();
   return  $verCtrl->sendVerifyEmail($req->email);
});

/* User Post Requests*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/user', [App\Http\Controllers\UserController::class, 'get_user']);
    Route::post('/user/info', [App\Http\Controllers\UserController::class, 'info']);
    Route::post('/user/changePP', [App\Http\Controllers\UserController::class, 'changePhoto']);
    Route::post('/user/all', [App\Http\Controllers\UserController::class, 'get_users']);
    Route::post('/user/logout', [App\Http\Controllers\UserController::class, 'logout']);
    Route::post('/user/update', [App\Http\Controllers\UserController::class, 'update']);
    Route::post('/user/add', [App\Http\Controllers\UserController::class, 'register']);
    Route::post('/user/delete', [App\Http\Controllers\UserController::class, 'delete']);
    Route::post('/user/search', [App\Http\Controllers\UserController::class, 'search_user']);
    Route::post('/user/verify/email/send', [App\Http\Controllers\VerifyController::class, 'send_verify_mail']);
    Route::post('/user/check', [App\Http\Controllers\UserController::class, 'checkUser']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/shelf/info', [App\Http\Controllers\ShelfController::class, 'info']);
    Route::post('/shelf/get', [App\Http\Controllers\ShelfController::class, 'get_shelf']);
    Route::post('/shelf/all', [App\Http\Controllers\ShelfController::class, 'get_shelves']);
    Route::post('/shelf/add', [App\Http\Controllers\ShelfController::class, 'create_shelf']);
    Route::post('/shelf/update', [App\Http\Controllers\ShelfController::class, 'update_shelf']);
    Route::post('/shelf/delete', [App\Http\Controllers\ShelfController::class, 'delete_shelf']);
    Route::post('/shelf/search', [App\Http\Controllers\ShelfController::class, 'search_shelf']);
});

/* Shelf Post Requests*/
Route::middleware('auth:sanctum')->group(function () {
});