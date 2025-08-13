<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.home');
})->name('admin.home');


Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
Route::get('/admin/yard', [AdminController::class, 'yard'])->name('admin.yard');
Route::get('/admin/contracts', [AdminController::class, 'contracts'])->name('admin.contracts');
Route::get('/admin/invoices', [AdminController::class, 'invoices'])->name('admin.invoices');
Route::get('/admin/payments', [AdminController::class, 'payments'])->name('admin.payments');