<?php

use Illuminate\Http\Request;

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

Route::get('/user', function () {
  return Auth::user();
})->name('user');

Route::get('/store', function () {
  return Auth::guard('store')->user();
})->name('store');

Route::get('/reflesh-token', function (Illuminate\Http\Request $request) {
  $request->session()->regenerateToken();

  return response()->json();
});

Auth::routes();

Route::post('/register', 'Auth\RegisterController@register')->name('register');

Route::post('/register-store', 'Auth\RegisterStoreController@register')->name('register-store');

Route::post('/edit-user-profile', 'EditUserProfileController@edit')->name('edit');

Route::post('/edit-store-profile', 'EditStoreProfileController@edit')->name('edit-store');

Route::post('/login', 'Auth\LoginController@login')->name('login');

Route::post('/login-store', 'Auth\LoginStoreController@login')->name('login-store');

Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

Route::post('/pass-reminder-send', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('pass-reminder-send');

Route::post('/pass-reminder-send-store', 'Auth\ForgotPasswordStoreController@sendResetLinkEmail')->name('pass-reminder-send-store');

Route::post('/check-token', 'Auth\ResetPasswordController@checkToken')->name('check-token');

Route::post('/check-token-store', 'Auth\ResetPasswordStoreController@checkToken')->name('check-token-store');

Route::post('/pass-reset', 'Auth\ResetPasswordController@reset')->name('pass-reset');

Route::post('/pass-reset-store', 'Auth\ResetPasswordStoreController@reset')->name('pass-reset-store');

Route::get('/item', 'ItemController@readOneItem')->name('item.read-one-item');

Route::get('/items', 'ItemController@read')->name('item.read');

Route::get('/items/sell', 'ItemController@readSellItem')->name('item.read-sell-item');

Route::get('/items/sold', 'ItemController@readSoldItem')->name('item.read-sold-item');

Route::get('/items/buy', 'ItemController@readBuyItem')->name('item.read-buy-item');

Route::get('/items/delete', 'ItemController@delete')->name('item.delete');

Route::post('/items', 'ItemController@create')->name('item.create');

Route::post('/items/edit', 'ItemController@update')->name('item.update');

Route::get('/history/create', 'historyController@create')->name('history.create');

Route::get('/history/delete', 'historyController@delete')->name('history.delete');
