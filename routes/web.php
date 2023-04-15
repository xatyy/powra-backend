<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// Route::get('/email', function () {
//     return view('emails.powra', [
//       'powra_result' => \App\PowraReport::with('user')->find(4)
//     ]);
// });

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
    Route::post('get-rapoarte-data', '\App\Http\Controllers\ReportsController@generate_report')->middleware('admin.user');
    Route::post('get-rapoarte-data-carchecklist', '\App\Http\Controllers\ReportsController@generate_report_car_checklist')->middleware('admin.user');
    Route::post('get-rapoarte-data-powra', '\App\Http\Controllers\ReportsController@generate_report_powra')->middleware('admin.user');
    Route::get('export-data', '\App\Http\Controllers\ReportsController@generate_report')->middleware('admin.user');
});
