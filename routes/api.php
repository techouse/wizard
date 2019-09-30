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

Route::namespace('Api')
     ->as('api.')
     ->group(function () {
         Route::middleware('auth:api')
              ->group(function () {

                  Route::apiResource('users', 'UserController')
                       ->except(['create', 'edit']);

                  Route::delete('users-bulk', 'UserController@bulkDestroy')
                       ->name('users.destroy.bulk');

                  Route::get('me', 'UserController@me')
                       ->name('users.me');
              });
     });
