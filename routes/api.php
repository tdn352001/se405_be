<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('/index', 'App\Http\Controllers\TestController@index');

Route::group(['namespace' => 'Api'], function () {
    Route::any('/login','LoginController@login');
    Route::any('/get_profile','LoginController@get_profile')->middleware('UserCheck');
    Route::any('/update_profile','LoginController@update_profile')->middleware('UserCheck');
    Route::any('/bind_fcmtoken','LoginController@bind_fcmtoken')->middleware('UserCheck');
    Route::any('/contact','LoginController@contact')->middleware('UserCheck');
    Route::any('/upload_photo','LoginController@upload_photo')->middleware('UserCheck');
    Route::any('/send_notice','LoginController@send_notice')->middleware('UserCheck');
    Route::any('/get_rtc_token','AccessTokenController@get_rtc_token')->middleware('UserCheck');
    Route::any('/send_notice_test','LoginController@send_notice_test');
});
