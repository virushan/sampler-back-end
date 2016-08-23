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

use Illuminate\Support\Facades\Route;

/*

Route::post('oauth/access_token', function() {
    //$auth = Authorizer::issueAccessToken();
    //dd(App\Http\Controllers\API\usersAPIController::show(\Authorizer::getResourceOwnerId()));
    return \Response::json(\Authorizer::issueAccessToken());
});


Route::get('oauth/sign_out', ['middleware' => 'oauth', function(){
    //\Authorizer::getChecker()->getAccessToken()->expire();
    dd(Auth::loginUsingId(\Authorizer::getResourceOwnerId()));
    return \Response::json(['success' => true, 'message' => 'users signed out successfully']);
}]);
*/

/*
|--------------------------------------------------------------------------
| API routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'api', 'namespace' => 'API'], function () {
    Route::group(['prefix' => 'v1'], function () {
        require config('infyom.laravel_generator.path.api_routes');
    });
});
