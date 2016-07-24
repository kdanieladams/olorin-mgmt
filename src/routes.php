<?php

/**
 * Auth routes
 */
Route::get('/login', [
    'as' => 'auth.login',
    'middleware' => 'web',
    'uses' => '\Olorin\Mgmt\LoginLogoutController@loginForm'
]);
Route::post('/login', [
    'as' => 'auth.loginPost',
    'middleware' => 'web',
    // 'uses' => 'App\Http\Controllers\Auth\AuthController@postLogin'
    'uses' => '\Olorin\Mgmt\LoginLogoutController@loginPost'
]);

Route::get('/logout', [
    'as' => 'auth.logout',
    'middleware' => 'web',
    'uses' => '\Olorin\Mgmt\LoginLogoutController@logout'
]);

Route::get('/denied', function(){
    abort(401);
});

Route::get('/test', function(){
    return view("mgmt::test");
});

/**
 * Mgmt routes
 */
Route::group(['prefix' => 'mgmt', 'middleware' => ['web', 'auth']], function(){
    Route::get('/', ['as' => 'mgmt.index', 'uses' => '\Olorin\Mgmt\MgmtController@index']);
    Route::get('/{command}/{model?}/{id?}', ['as' => 'mgmt.command', 'uses' => '\Olorin\Mgmt\MgmtController@command']);
    Route::post('/{command}/{model?}/{id?}', ['as' => 'mgmt.post', 'uses' => '\Olorin\Mgmt\MgmtController@command']);
});