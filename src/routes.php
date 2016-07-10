<?php

/**
 * Auth routes
 */
//Route::get('/login', [
//    'as' => 'auth.login',
//    'uses' => 'Auth\LoginLogoutController@login'
//]);
//Route::post('/login', 'Auth\AuthController@postLogin');
//
//Route::get('/logout', [
//    'as' => 'auth.logout',
//    'uses' => 'Auth\LoginLogoutController@logout'
//]);
//
//Route::get('/denied', function(){
//    abort(401);
//});

Route::get('/test', function(){
    return view("MGMT::test");
});

/**
 * Mgmt routes
 */
Route::group(['prefix' => 'mgmt', 'middleware' => ['auth', 'can-view-mgmt']], function(){
    Route::get('/', ['as' => 'mgmt.index', 'uses' => '\Olorin\Mgmt\MgmtController@index']);
    Route::get('/{command}/{model?}/{id?}', ['as' => 'mgmt.command', 'uses' => '\Olorin\Mgmt\MgmtController@command']);
    Route::post('/{command}/{model?}/{id?}', ['as' => 'mgmt.post', 'uses' => '\Olorin\Mgmt\MgmtController@command']);
});