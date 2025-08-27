<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContainerController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PolicyController;
use App\Models\Container;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\invoice;
use Illuminate\Support\Facades\Route;

Route::get('/', [AdminController::class, 'dashboard'])->middleware('auth')->name('admin.home');

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'loginForm')->name('login.form');
    Route::post('/login', 'login')->name('login');
    Route::post('/logout', 'logout')->name('logout');
});

Route::controller(CustomerController::class)->middleware('auth')->group(function () {
    Route::get('/users/customers', 'customers')->name('users.customers');
    Route::get('/users/customer/{id}', 'customerProfile')->name('users.customer.profile');
    Route::post('/users/customer/store', 'storeCustomer')->name('users.customer.store');
    Route::put('/users/customer/update/{id}', 'updateCustomer')->name('users.customer.update');
    Route::delete('/users/customer/delete/{id}', 'deleteCustomer')->name('users.customer.delete');
});

Route::controller(ContainerController::class)->middleware('auth')->group(function () {
    Route::get('/yard/containers', 'containers')->name('yard.containers');
    Route::get('/yard/containers/create', 'createContainer')->name('yard.containers.create');
    Route::post('/yard/containers/store', 'containerStore')->name('yard.containers.store');
    Route::put('/yard/containers/update/{id}', 'containerUpdate')->name('yard.containers.update');
    Route::get('/yard/containers/types', 'containersTypes')->name('yard.containers.types');
    Route::post('/yard/containers/types/store', 'containerTypeStore')->name('yard.containers.types.store');
    Route::put('/yard/containers/types/update/{id}', 'updateContainerType')->name('yard.containers.types.update');
    Route::delete('/yard/containers/types/delete/{id}', 'deleteContainerType')->name('yard.containers.types.delete');
    Route::post('/exit_permission', 'exitPermission')->name('exit.permission');
    Route::post('/entry_permission', 'entryPermission')->name('entry.permission');
});

Route::controller(PolicyController::class)->middleware('auth')->group(function () {
    Route::get('/policies', 'policies')->name('policies');
    Route::get('/policies/storage/create', 'storagePolicy')->name('policies.storage.create');
    Route::get('/policies/receive/create', 'createReceivePolicy')->name('policies.receive.create');
    Route::post('/policies/storage/store', 'storeStoragePolicy')->name('policies.storage.store');
    Route::post('/policies/receive/store', 'storeReceivePolicy')->name('policies.receive.store');
    Route::get('/policies/storage/details/{id}', 'storagePolicyDetails')->name('policies.storage.details');
    Route::get('/policies/receive/details/{id}', 'receivePolicyDetails')->name('policies.receive.details');
});

Route::controller(ContractController::class)->middleware('auth')->group(function () {
    Route::get('/contracts', 'contracts')->name('contracts');
    Route::get('/contracts/create', 'createContract')->name('contracts.create');
    Route::post('/contracts/store', 'storeContract')->name('contracts.store');
    Route::get('/contracts/{id}', 'contractDetails')->name('contracts.details');
    Route::post('/contracts/update/{id}', 'updateContract')->name('contracts.update');
});

Route::controller(InvoiceController::class)->middleware('auth')->group(function () {
    Route::get('/invoices', 'invoices')->name('invoices');
    Route::post('invoice/create', 'storeInvoice')->name('invoices.store');
    Route::put('invoice/update/{id}', 'updateInvoice')->name('invoices.update');
});

Route::controller(AccountingController::class)->middleware('auth')->group(function () {
    Route::get('/admin/money/tree', 'tree')->name('admin.money.tree');
    Route::post('/admin/money/tree/create/root', 'createRoot')->name('admin.create.root');
    Route::get('admin/money/entries', 'entries')->name('admin.money.entries');
    Route::post('admin/money/entries/create/voucher', 'createVoucher')->name('admin.create.voucher');
    Route::delete('admin/money/entries/delete/{id}', 'deleteVoucher')->name('admin.delete.voucher');
    Route::post('admin/money/entries/create/journal', 'createJournal')->name('admin.create.journal');
});