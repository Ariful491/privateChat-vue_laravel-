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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('userlist', 'MessageController@user_list');
Route::get('usermessage/{id}', 'MessageController@user_message');

Route::post('sendmessage', 'MessageController@send_message');



Route::get('single_message_delete/{id}', 'MessageController@gettest');
Route::get('delete_all_message/{id}', 'MessageController@all_message');




















