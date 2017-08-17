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

Route::get('/', 'HomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::post('/send', 'HomeController@send')->name('send');

Route::get('/contacts', 'ContactController@index')->name('contacts');
Route::post('/contacts/ajax', 'ContactController@ajax')->name('contacts.ajax');
Route::post('/contacts/import', 'ContactController@import')->name('contacts.import');