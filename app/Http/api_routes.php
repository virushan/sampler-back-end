<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where all API routes are defined.
|
*/

Route::post('sign_in', 'usersAPIController@signIn');
Route::get('sign_out', 'usersAPIController@signOut');
Route::resource('users', usersAPIController::class);


Route::put('accounts/{id}/send_funds', 'AccountAPIController@sendMoney');
Route::resource('accounts', AccountAPIController::class);