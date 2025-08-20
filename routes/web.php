<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Models\Contract;
use App\Models\Customer;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $customers = Customer::all()->count();
    $contracts = Contract::all()->count();
    return view('admin.home', compact('customers', 'contracts'));
})->name('admin.home');

Route::get('/users/customers', [CustomerController::class, 'customers'])->name('users.customers');
Route::get('/users/{id}', [CustomerController::class, 'userProfile'])->name('user.profile');
Route::post('/users/customer/create', [CustomerController::class, 'storeCustomer'])->name('users.customer.store');
Route::put('/users/customer/update/{id}', [CustomerController::class, 'updateCustomer'])->name('users.customer.update');
Route::delete('/users/customer/delete/{id}', [CustomerController::class, 'deleteCustomer'])->name('users.customer.delete');

Route::get('/admin/yard/containers', [AdminController::class, 'containers'])->name('admin.yard.containers');
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

Route::get('/admin/money/tree', [AccountingController::class, 'tree'])->name('admin.money.tree');
Route::post('/admin/money/tree/create/root', [AccountingController::class, 'createRoot'])->name('admin.create.root');
Route::get('admin/money/entries', [AccountingController::class, 'entries'])->name('admin.money.entries');
Route::post('admin/money/entries/create/voucher', [AccountingController::class, 'createVoucher'])->name('admin.create.voucher');
Route::delete('admin/money/entries/delete/{id}', [AccountingController::class, 'deleteVoucher'])->name('admin.delete.voucher');
Route::post('admin/money/entries/create/journal', [AccountingController::class, 'createJournal'])->name('admin.create.journal');