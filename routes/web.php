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

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('auth/telegram/callback', 'Auth\TelegramController@handleTelegramCallback')->name('auth.telegram.handle');

Route::get('/', 'HomeController@index')->name('home');
Route::resource('instagramProfiles', 'InstagramProfilesController')->except(['edit', 'update']);
Route::get('/instagram', 'HomeController@index')->name('home');
