<?php

/**
 * Auth routes
 */
Route::get('/mgmt/login', [
    'as' => 'mgmt.login',
    'middleware' => 'web',
    'uses' => '\Olorin\Auth\LoginLogoutController@loginForm'
]);
Route::post('/mgmt/login', [
    'as' => 'mgmt.loginPost',
    'middleware' => 'web',
    'uses' => '\Olorin\Auth\LoginLogoutController@login'
]);

Route::get('/mgmt/logout', [
    'as' => 'mgmt.logout',
    'middleware' => 'web',
    'uses' => '\Olorin\Auth\LoginLogoutController@logout'
]);

Route::get('/denied', function(){
    abort(401);
});

/**
 * Mgmt routes
 */
Route::group(['prefix' => 'mgmt', 'middleware' => ['web', 'auth']], function(){
    Route::get('/', [
        'as' => 'mgmt.index',
        'uses' => '\Olorin\Mgmt\MgmtController@index'
    ]);

    Route::post('/jqdt/list/{model}', [
        'as' => 'mgmt.jqdt.list',
        'uses' => '\Olorin\Support\JqdtController@getListPage'
    ]);

    Route::get('/{command}/{model?}/{id?}', [
        'as' => 'mgmt.command',
        'uses' => '\Olorin\Mgmt\MgmtController@command'
    ]);

    Route::post('/{command}/{model?}/{id?}', [
        'as' => 'mgmt.post',
        'uses' => '\Olorin\Mgmt\MgmtController@command'
    ]);
});