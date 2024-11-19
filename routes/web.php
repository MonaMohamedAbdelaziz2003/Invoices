<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\customerReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\invoices_report;
use App\Http\Controllers\InvoicesAttachmentsController;
use App\Http\Controllers\InvoicesController;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\invoicesReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SectionsController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('auth.login');
    // return view('welcome');
});

// ////////////
Route::group(['middleware' => ['auth']], function() {
    Route::resource('users',UserController::class);
    Route::resource('roles',RoleController::class);
    Route::resource('products',ProductController::class);
});

// auth ui
Auth::routes();
// Auth::routes(['register'=>false]);

Route::get('/home', [HomeController::class, 'index'])->name('home');


Route::get('/sendmail',[InvoicesController::class,'send']);

// export
Route::get('/export_invoices', [InvoicesController::class, 'export']);

// invoices
// Route::resource('invoices','InvoicesController');
Route::resource('invoices',InvoicesController::class);
Route::resource('InvoiceAttachments',InvoicesAttachmentsController::class);
Route::resource('Archive',ArchiveController::class);
// Route::resource('section/id',[InvoicesController::class,'getproduct']);
Route::get('section/{id}', [InvoicesController::class, 'getproduct']);
Route::get('InvoicesDetails/{id}', [InvoicesDetailsController::class, 'edit']);
Route::get('View_file/{invoice_number}/{file_name}', [InvoicesDetailsController::class, 'open_file']);
Route::get('download/{invoice_number}/{file_name}', [InvoicesDetailsController::class, 'download']);
Route::get('edit_invoice/{id}', [InvoicesController::class, 'edit']);
Route::get('delete_invoice/{id}', [InvoicesController::class, 'destroy']);
Route::get('Status_show/{id}', [InvoicesController::class, 'show'])->name('Status_show');
Route::post('Status_Update/{id}', [InvoicesController::class, 'Status_Update'])->name('Status_Update');
// Route::get('Status_Update/{id}', [InvoicesController::class, 'Status_Update']);
Route::post('delete_file', [InvoicesDetailsController::class, 'destroy'])->name('delete_file');
//
Route::get('Invoices_Paid',[InvoicesController::class,"Invoices_Paid"]);
Route::get('Invoices_UnPaid',[InvoicesController::class,"Invoices_UnPaid"]);
Route::get('Invoices_Partial',[InvoicesController::class,"Invoices_Partial"]);
Route::get('Print_invoice/{id}',[InvoicesController::class,"Print_invoice"]);
Route::get('MarkAsRead_all',[InvoicesController::class,"MarkAsRead_all"]);
// Route::get('markAsRead',[InvoicesController::class,"markAsRead"]);
// Route::get('trash',[InvoicesAttachmentsController::class,"index"]);

//  report
Route::get('invoices_report',[invoicesReportController::class,"index"]);
Route::get('customers_report',[customerReportController::class,"index"]);
Route::post('Search_invoices',[invoicesReportController::class,"Search_invoices"]);
Route::post('Search_customers',[customerReportController::class,"Search_customers"]);

//

Route::resource('section',SectionsController::class);
Route::resource('product',ProductController::class);
//
Route::get('/{page}', [AdminController::class, 'index']);
// Route::get('/{page}', 'AdminController@index');
