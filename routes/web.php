<?php

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

// 前台入口
Route::get('/', function () {
    return view('index');
});

// 管理后台入口
Route::get('/backend',function (){
    return view('backend');
});

Route::group(['prefix' => 'oauth', 'middleware' => 'guest'], function () {
    Route::get('/{driver}', 'Auth\AuthenticationController@redirectToProvider');
    Route::get('/{driver}/callback', 'Auth\AuthenticationController@handleProviderCallback');
});
