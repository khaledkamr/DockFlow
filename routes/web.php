<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContainerController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PolicyController;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\invoice;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $customers = Customer::all()->count();
    $contracts = Contract::all()->count();
    $invoices = invoice::all()->count();
    return view('admin.home', compact('customers', 'contracts', 'invoices'));
})->name('admin.home');

Route::get('/users/customers', [CustomerController::class, 'customers'])->name('users.customers');
Route::get('/users/customer/{id}', [CustomerController::class, 'customerProfile'])->name('users.customer.profile');
Route::post('/users/customer/store', [CustomerController::class, 'storeCustomer'])->name('users.customer.store');
Route::put('/users/customer/update/{id}', [CustomerController::class, 'updateCustomer'])->name('users.customer.update');
Route::delete('/users/customer/delete/{id}', [CustomerController::class, 'deleteCustomer'])->name('users.customer.delete');

Route::get('/yard/containers', [ContainerController::class, 'containers'])->name('yard.containers');
Route::get('/yard/containers/create', [ContainerController::class, 'createContainer'])->name('yard.containers.create');
Route::post('/yard/containers/store', [ContainerController::class, 'containerStore'])->name('yard.containers.store');
Route::put('/yard/containers/update/{id}', [ContainerController::class, 'containerUpdate'])->name('yard.containers.update');
Route::get('/yard/containers/types', [ContainerController::class, 'containersTypes'])->name('yard.containers.types');
Route::post('/yard/containers/types/store', [ContainerController::class, 'containerTypeStore'])->name('yard.containers.types.store');
Route::put('/yard/containers/types/update/{id}', [ContainerController::class, 'updateContainerType'])->name('yard.containers.types.update');
Route::delete('/yard/containers/types/delete/{id}', [ContainerController::class, 'deleteContainerType'])->name('yard.containers.types.delete');

Route::controller(PolicyController::class)->group(function () {
    Route::get('/policies', 'policies')->name('policies');
    Route::get('/policies/storage/create', 'storagePolicy')->name('policies.storage.create');
    Route::get('/policies/receive/create', 'createReceivePolicy')->name('policies.receive.create');
    Route::post('/policies/storage/store', 'storeStoragePolicy')->name('policies.storage.store');
    Route::post('/policies/receive/store', 'storeReceivePolicy')->name('policies.receive.store');
    Route::get('/policies/storage/details/{id}', 'storagePolicyDetails')->name('policies.storage.details');
    Route::get('/policies/receive/details/{id}', 'receivePolicyDetails')->name('policies.receive.details');
});

Route::get('/contracts', [ContractController::class, 'contracts'])->name('contracts');
Route::get('/contracts/create', [ContractController::class, 'createContract'])->name('contracts.create');
Route::post('/contracts/store', [ContractController::class, 'storeContract'])->name('contracts.store');
Route::get('/contracts/{id}', [ContractController::class, 'contractDetails'])->name('contracts.details');
Route::post('/contracts/update/{id}', [ContractController::class, 'updateContract'])->name('contracts.update');

Route::get('/invoices', [InvoiceController::class, 'invoices'])->name('invoices');
Route::post('invoice/create', [InvoiceController::class, 'storeInvoice'])->name('invoices.store');
Route::put('invoice/update/{id}', [InvoiceController::class, 'updateInvoice'])->name('invoices.update');

Route::get('/admin/money/tree', [AccountingController::class, 'tree'])->name('admin.money.tree');
Route::post('/admin/money/tree/create/root', [AccountingController::class, 'createRoot'])->name('admin.create.root');
Route::get('admin/money/entries', [AccountingController::class, 'entries'])->name('admin.money.entries');
Route::post('admin/money/entries/create/voucher', [AccountingController::class, 'createVoucher'])->name('admin.create.voucher');
Route::delete('admin/money/entries/delete/{id}', [AccountingController::class, 'deleteVoucher'])->name('admin.delete.voucher');
Route::post('admin/money/entries/create/journal', [AccountingController::class, 'createJournal'])->name('admin.create.journal');