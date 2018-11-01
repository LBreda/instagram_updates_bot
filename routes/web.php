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
    try {
        $privacy_data = \Storage::disk('local')->get('privacy_policy.md');
    } catch (FileNotFoundException $e) {
        $privacy_data = "Create a `privacy_policy.md` file in /storage/app.";
    }
    return view('auth.login', compact('privacy_data'));
})->name('login');
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

Route::get('auth/telegram/callback', 'Auth\TelegramController@handleTelegramCallback')->name('auth.telegram.handle');

Route::get('/', 'HomeController@index')->name('home');
Route::resource('instagramProfiles', 'InstagramProfilesController')->except(['edit', 'update']);
Route::get('/instagram', 'HomeController@index')->name('home');
Route::get('/privacy', 'PrivacyController@index')->name('privacy');
