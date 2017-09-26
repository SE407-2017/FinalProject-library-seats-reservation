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

Route::group(['prefix' => 'reserve', 'middleware' => ['auth']], function() {
    Route::get('/home', 'ReserveController@index');
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth.admin']], function() {
    Route::get('/home', 'AdminController@index');
});