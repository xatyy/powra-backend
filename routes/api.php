<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\UserController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/recover-password', [UserController::class, 'forgot_password_form']);

Route::group(['namespace' => 'Api'], function() {
    
    Route::prefix('/user')->group(function() {
        Route::get('/check',                   [UserController::class, 'checkToken']);
        Route::post('/refresh',                [UserController::class, 'refreshToken']);
        Route::post('/login',                  [UserController::class, 'login']);
        Route::post('/delete_account',         [UserController::class, 'delete_account']);
        Route::post('/edit',                   [UserController::class, 'edit']);
        Route::post('/register',               [UserController::class, 'register']);
        Route::post('/recover',                [UserController::class, 'forgotPassword']);
        Route::post('/facebook',               [UserController::class, 'facebook']);
        Route::post('/apple',                  [UserController::class, 'appleLogin']);
      
        Route::get('/getStatics',             [ApiController::class, 'getStatics']);
        Route::get('/getCustodyCars',             [ApiController::class, 'getCustodyCars']);
        Route::get('/getPowraData',             [ApiController::class, 'getPowraData']);
        Route::post('/get_updated_user', [UserController::class, 'get_updated_user']);
        
        Route::get('recover-password/{hash}', [UserController::class, 'forgot_password_form']);
        Route::post('/reset-pass', [UserController::class, 'forgotPasswordVerify']);
        Route::post('/send-delivery-report', [ApiController::class, 'send_delivery_report']);
        Route::post('/send-car-checklist-report', [ApiController::class, 'send_car_checklist_report']);
        Route::post('/send-powra-report', [ApiController::class, 'send_powra_report']);
        Route::post('/accept-report', [ApiController::class, 'accept_report']);
        Route::post('/remove-notification', [ApiController::class, 'removeNotification']);
        Route::post('/reject-notification', [ApiController::class, 'rejectNotification']);
        Route::get('/get-delivery-receipt', [ApiController::class, 'get_delivery_receipt']);
        Route::get('/get-car-checklist', [ApiController::class, 'get_car_checklist']);
        Route::get('/get-powra-reports', [ApiController::class, 'get_powra_reports']);
        Route::get('/get-notifications', [ApiController::class, 'getNotifications']);
    });
    
});