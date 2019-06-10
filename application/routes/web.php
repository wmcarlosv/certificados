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
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/certificate/{id}/{email}/{fn}/{ln}/{dni}','CertificatesController@view_certificate');

Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function(){
	Route::resource('certificates','CertificatesController');
	Route::get('certificates/send-certificate/{id}','CertificatesController@send_certificate')->name('send_certificate');
	Route::post('certificates/store_send/{id}','CertificatesController@store_send')->name('store_send');
	Route::post('certificates/load_cvs_fle','CertificatesController@load_cvs_fle')->name('load_cvs_fle');
	Route::get('certificates/preview_pdf/{id}','CertificatesController@preview_pdf')->name('preview_pdf');
});