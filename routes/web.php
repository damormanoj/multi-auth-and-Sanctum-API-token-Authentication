<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/users/logout', 'Auth\LoginController@userLogout')->name('user.logout');


Route::get('/adminhome', 'AdminHomeController@index')->name('admin.dashboard');
Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::get('login', 'LoginController@index')->name('admin.login');
    Route::post('login', 'LoginController@login')->name('admin.login.submit');
    Route::get('/logout', 'LoginController@logout')->name('admin.logout');

    // Password reset routes
  Route::post('/password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('admin.password.email');
  Route::get('/password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('admin.password.request');
  Route::post('/password/reset', 'ResetPasswordController@reset');
  Route::get('/password/reset/{token}', 'ResetPasswordController@showResetForm')->name('admin.password.reset');
});
