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

//Admin panel routes
Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth'], function () {

	//Site management
    Route::resource('outreach-management', 'SiteController');
    Route::put('outreach-management/{site}/status-update', 'SiteController@status')->name('outreach-management.status-update');

	//Backlink management
    Route::resource('outreach-backlinks', 'BacklinkController');
    Route::put('outreach-backlinks/{backlink}/status-update', 'BacklinkController@status')->name('outreach-backlinks.status-update');

    //Invoice management
    Route::resource('outreach-invoices', 'InvoiceController');
    Route::get('outreach-invoices/{site}/get-data', 'InvoiceController@data')->name('outreach-invoices.site-data');
    Route::put('outreach-invoices/{invoice}/status-update', 'InvoiceController@status')->name('outreach-invoices.status-update');
    Route::put('outreach-invoices/{invoice}/proceed', 'InvoiceController@proceed')->name('outreach-invoices.proceed');
    Route::post('outreach-invoices/{invoice}/receipt', 'InvoiceController@receipt')->name('outreach-invoices.receipt');
    Route::get('outreach-invoices/{invoice}/print', 'InvoiceController@print')->name('outreach-invoices.print');

    //Settings
    Route::get('outreach-settings', 'SettingController@index')->name('outreach-settings.index');
    Route::post('outreach-settings', 'SettingController@update')->name('outreach-settings.update');
    Route::post('outreach-settings/update-module', 'SettingController@updateModule')->name('outreach-settings.update-module');

    //Comments management
    Route::resource('outreach-comments', 'CommentController');
});

//Member panel routes
Route::group(['namespace' => 'Member', 'prefix' => 'member', 'as' => 'member.', 'middleware' => 'auth'], function () {

	//Site management
    Route::resource('outreach-management', 'SiteController');
    Route::put('outreach-management/{site}/status-update', 'SiteController@status')->name('outreach-management.status-update');

    //Backlink management
    Route::resource('outreach-backlinks', 'BacklinkController');
    Route::put('outreach-backlinks/{backlink}/status-update', 'BacklinkController@status')->name('outreach-backlinks.status-update');

    //Invoice management
    Route::resource('outreach-invoices', 'InvoiceController');
    Route::get('outreach-invoices/{site}/get-data', 'InvoiceController@data')->name('outreach-invoices.site-data');
    Route::put('outreach-invoices/{invoice}/status-update', 'InvoiceController@status')->name('outreach-invoices.status-update');
    Route::put('outreach-invoices/{invoice}/proceed', 'InvoiceController@proceed')->name('outreach-invoices.proceed');
    Route::put('outreach-invoices/{invoice}/review', 'InvoiceController@review')->name('outreach-invoices.review');
    Route::post('outreach-invoices/{invoice}/receipt', 'InvoiceController@receipt')->name('outreach-invoices.receipt');
    Route::get('outreach-invoices/{invoice}/print', 'InvoiceController@print')->name('outreach-invoices.print');
});