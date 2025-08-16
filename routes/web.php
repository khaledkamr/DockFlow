<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.home');
})->name('admin.home');

Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
Route::post('admin/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
Route::put('admin/users/update/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
Route::delete('admin/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
Route::get('/admin/users/admins', [AdminController::class, 'admins'])->name('admin.users.admins');
Route::get('/admin/yard', [AdminController::class, 'yard'])->name('admin.yard');
Route::get('/admin/yard/add', [AdminController::class, 'yardAdd'])->name('admin.yard.add');
Route::post('/admin/yard/containers/create', [AdminController::class, 'containerCreate'])->name('admin.yard.containers.create');
Route::get('/admin/yard/containers/types', [AdminController::class, 'containersTypes'])->name('admin.yard.containers.types');
Route::post('/admin/yard/containers/types/create', [AdminController::class, 'containerTypeCreate'])->name('admin.yard.containers.types.create');
Route::put('/admin/yard/containers/types/update/{id}', [AdminController::class, 'updateContainerType'])->name('admin.yard.containers.types.update');
Route::delete('/admin/yard/containers/types/delete/{id}', [AdminController::class, 'deleteContainerType'])->name('admin.yard.containers.types.delete');
Route::get('/admin/contracts', [AdminController::class, 'contracts'])->name('admin.contracts');
Route::get('/admin/contracts/create', [AdminController::class, 'createContract'])->name('admin.contracts.create');
Route::post('/admin/contracts/store', [AdminController::class, 'storeContract'])->name('admin.contracts.store');
Route::get('/admin/contracts/details/{id}', [AdminController::class, 'contractDetails'])->name('admin.contracts.details');
Route::post('admin/invoice/create', [AdminController::class, 'createInvoice'])->name('admin.invoice.create');
Route::post('/admin/exit_permission/create', [AdminController::class, 'exitPermission'])->name('admin.exitPermission.create');
Route::get('/admin/invoices', [AdminController::class, 'invoices'])->name('admin.invoices');
Route::get('/admin/payments', [AdminController::class, 'payments'])->name('admin.payments');