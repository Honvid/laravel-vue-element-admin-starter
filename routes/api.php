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
        'prefix' => 'users'
    ], function () {
        Route::post('login', 'Auth\AuthenticationController@login');
        Route::post('register', 'Auth\AuthenticationController@register');
        Route::get('logout', 'Auth\AuthenticationController@logout')
            ->middleware('auth:api');
        Route::get('info', 'Auth\AuthenticationController@user')
            ->middleware('auth:api');
    });

    Route::apiResource('users', 'UserController')->middleware('auth:api');
    Route::apiResource('abilities', 'Auth\AbilityController')->middleware('auth:api');
    Route::apiResource('roles', 'Auth\RoleController')->middleware('auth:api');
    Route::apiResource('menus', 'MenuController')->middleware('auth:api');
    Route::get('menu-list', 'MenuController@list')->middleware('auth:api');
    Route::get('my-menu', 'MenuController@my')->middleware('auth:api');
    Route::get('ability-groups', 'Auth\AbilityController@groups')->middleware('auth:api');
});