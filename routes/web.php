<?php

use Illuminate\Support\Facades\Route;

//Route::get('koko', function () { return 'welcome man'; })->middleware('homepage');

Route::get('/', function () {
    
    return view('auth.login');
})->middleware('homepage');

// Auth Laravel Routes
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');

// Routes of Invoices http://laravel.local/invoices/Controller
Route::resource('invoices', 'InvoicesController');

// Routes of Products http://laravel.local/products/Controller
Route::resource('products', 'ProductsController');

// Routes of Sections http://laravel.local/sections/Controller
Route::resource('sections', 'SectionsController');

// 
Route::resource('InvoicesAttachments', 'InvoicesAttachmentController');


// Routes of Products http://laravel.local/section/id/Controller
Route::get('section/{id}', 'InvoicesController@getProducts');
Route::get('InvoicesDetails/{id}', 'InvoicesDetailsController@edit');

Route::get('download/{invoice_number}/{file_name}', 'InvoicesDetailsController@get_file');
Route::get('View_file/{invoice_number}/{file_name}', 'InvoicesDetailsController@open_file');
Route::post('delete_file', 'InvoicesDetailsController@destroy')->name('delete_file');

Route::get('edit_invoice/{id}','InvoicesController@edit');

Route::get('/Status_show/{id}', 'InvoicesController@show')->name('Status_show');
Route::post('/Status_Update/{id}', 'InvoicesController@Status_Update')->name('Status_Update');

Route::get('Invoice_Paid','InvoicesController@Invoice_Paid');
Route::get('Invoice_UnPaid','InvoicesController@Invoice_UnPaid');
Route::get('Invoice_Partial','InvoicesController@Invoice_Partial');
Route::get('Print_invoice/{id}','InvoicesController@Print_invoice');
Route::get('export_invoices', 'InvoicesController@export');

Route::resource('Archive', 'InvoiceAchiveController');
/**Permission */
Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles','RoleController');
    Route::resource('users','UserController');
});

Route::get('invoices_report', 'InvoicesReportController@index');
Route::post('Search_invoices', 'InvoicesReportController@Search_invoices');

Route::get('customers_report', 'CustomersReportController@index')->name("customers_report");
Route::post('Search_customers', 'CustomersReportController@Search_customers');
// Notifcation Action
Route::get('MarkAsRead_all','InvoicesController@MarkAsRead_all')->name('MarkAsRead_all');
Route::get('unreadNotifications_count', 'InvoicesController@unreadNotifications_count')->name('unreadNotifications_count');
Route::get('unreadNotifications', 'InvoicesController@unreadNotifications')->name('unreadNotifications');

//  Routes of theme dashboard
Route::get('/{page}', 'AdminController@index');

