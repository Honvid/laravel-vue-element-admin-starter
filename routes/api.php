<?php

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

Route::group([
    'prefix' => 'v1'
], function () {
    Route::group([
        'prefix' => 'user'
    ], function () {
        Route::post('login', 'Auth\AuthenticationController@login')->name('user.login');
        Route::post('register', 'Auth\AuthenticationController@register')->name('user.register');
        Route::get('logout', 'Auth\AuthenticationController@logout')
            ->middleware('auth:api')
            ->name('user.logout');;
        Route::get('info', 'Auth\AuthenticationController@user')
            ->middleware('auth:api')
            ->name('user.info');;
    });
});
