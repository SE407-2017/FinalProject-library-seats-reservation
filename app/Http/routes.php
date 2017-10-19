<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/* Route::get('/', function () {
    return view('welcome');
}); */

Route::get('/', 'Auth\JaccountController@login');
Route::get('/auth/login', 'Auth\JaccountController@login');
Route::get('/forbidden', 'Auth\JaccountController@forbidden');

Route::group(['prefix' => 'reserve', 'middleware' => ['auth']], function() {
    Route::get('/home', 'ReserveController@index');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth.admin']], function() {
    Route::get('/home', 'AdminController@index');
});

Route::group(['prefix' => 'api/user', 'middleware' => ['auth']], function() {
    Route::post('/reservation/add', 'ReserveController@apiReservationAdd');
});

Route::group(['prefix' => 'api/admin', 'middleware' => ['auth.admin']], function() {
    Route::get('/floors/get', 'AdminController@apiFloorsGet');
    Route::get('/tables/get', 'AdminController@apiTablesGet');
    Route::get('/tables/get/{floor_id}', 'AdminController@apiTablesGetByFloor');
    Route::get('/tables/remove/{table_id}', 'AdminController@apiTablesRemove');
    Route::post('/tables/add', 'AdminController@apiTablesAdd');
});

Route::group(['prefix' => 'api/wechat'], function() {
    Route::post('/leave', 'WechatController@apiLeaveSeat');
});