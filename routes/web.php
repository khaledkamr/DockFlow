<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContainerController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\SupplierController;
use App\Models\Container;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\invoice;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Mailer\Transport;

Route::controller(AdminController::class)->middleware('auth')->group(function() {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/company/{company:uuid}', 'company')->name('company');
    Route::put('/company/update/{company:uuid}', 'updateCompany')->name('company.update');
    Route::get('users', 'users')->name('admin.users');
    Route::post('users/store', 'storeUser')->name('admin.users.store');
    Route::get('users/{user:uuid}', 'userProfile')->name('admin.user.profile');
    Route::get('user/{user:uuid}', 'myProfile')->name('user.profile');
    Route::patch('users/update/{user:uuid}', 'updateUser')->name('admin.users.update');
    Route::patch('user/update/{user:uuid}', 'updateMyUser')->name('admin.user.update');
    Route::patch('users/update/password/{user:uuid}', 'updatePassword')->name('admin.users.update.password');
    Route::delete('users/delete/{user:uuid}', 'deleteUser')->name('admin.users.delete');
    Route::get('roles', 'roles')->name('admin.roles');
    Route::post('roles/store', 'storeRole')->name('admin.roles.store');
    Route::put('roles/update/{role:id}', 'updateRole')->name('admin.roles.update');
    Route::delete('roles/delete/{role:id}', 'deleteRole')->name('admin.roles.delete');
});

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'loginForm')->name('login.form');
    Route::post('/login', 'login')->name('login');
    Route::post('/logout', 'logout')->name('logout');
});

Route::controller(CustomerController::class)->middleware('auth')->group(function () {
    Route::get('/customers', 'customers')->name('relation.customers');
    Route::get('/customer/{customer:uuid}', 'customerProfile')->name('users.customer.profile');
    Route::post('/customer/store', 'storeCustomer')->name('users.customer.store');
    Route::put('/customer/update/{customer:uuid}', 'updateCustomer')->name('users.customer.update');
    Route::delete('/customer/delete/{customer:uuid}', 'deleteCustomer')->name('users.customer.delete');
});

Route::controller(SupplierController::class)->middleware('auth')->group(function () {
    Route::get('/suppliers', 'suppliers')->name('relation.suppliers');
    Route::get('/supplier/{supplier:uuid}', 'supplierProfile')->name('users.supplier.profile');
    Route::post('/supplier/store', 'storeSupplier')->name('users.supplier.store');
    Route::put('/supplier/update/{supplier:uuid}', 'updateSupplier')->name('users.supplier.update');
    Route::delete('/supplier/delete/{supplier:uuid}', 'deleteSupplier')->name('users.supplier.delete');
});

Route::controller(ContainerController::class)->middleware('auth')->group(function () {
    Route::get('/yard/containers', 'containers')->name('yard.containers');
    Route::get('/yard/containers/create', 'createContainer')->name('yard.containers.create');
    Route::post('/yard/containers/store', 'containerStore')->name('yard.containers.store');
    Route::patch('/yard/containers/update/{container:uuid}', 'containerUpdate')->name('yard.containers.update');
    Route::get('/yard/containers/types', 'containersTypes')->name('yard.containers.types');
    Route::post('/yard/containers/types/store', 'containerTypeStore')->name('yard.containers.types.store');
    Route::put('/yard/containers/types/update/{id}', 'updateContainerType')->name('yard.containers.types.update');
    Route::delete('/yard/containers/types/delete/{id}', 'deleteContainerType')->name('yard.containers.types.delete');
    Route::get('/yard/containers/reports', 'reports')->name('yard.containers.reports');
    Route::post('/yard/containers/{container:uuid}/add/service', 'addService')->name('containers.add.service');
    Route::get('/yard/container/{container:uuid}', 'containerDetails')->name('container.details');
});

Route::controller(PolicyController::class)->middleware('auth')->group(function () {
    Route::get('/policies', 'policies')->name('policies');
    Route::get('/policies/storage/create', 'storagePolicy')->name('policies.storage.create');
    Route::get('/policies/receive/create', 'createReceivePolicy')->name('policies.receive.create');
    Route::post('/policies/storage/store', 'storeStoragePolicy')->name('policies.storage.store');
    Route::post('/policies/receive/store', 'storeReceivePolicy')->name('policies.receive.store');
    Route::get('/policies/storage/details/{policy:uuid}', 'storagePolicyDetails')->name('policies.storage.details');
    Route::get('/policies/receive/details/{policy:uuid}', 'receivePolicyDetails')->name('policies.receive.details');
    Route::get('/policies/services/create', 'servicePolicy')->name('policies.services.create');
    Route::post('/policies/services/store', 'storeServicePolicy')->name('policies.services.store');
    Route::get('/policies/services/details/{policy:uuid}', 'servicePolicyDetails')->name('policies.services.details');
});

Route::controller(TransactionController::class)->middleware('auth')->group(function () {
    Route::get('/transactions', 'transactions')->name('transactions');
    Route::get('/transactions/create', 'createTransaction')->name('transactions.create');
    Route::post('/transactions/store', 'storeTransaction')->name('transactions.store');
    Route::get('/transactions/{transaction:uuid}', 'transactionDetails')->name('transactions.details');
    Route::post('/transactions/item/store', 'storeItem')->name('transactions.item.store');
    Route::put('/transactions/item/update/{item:id}', 'updateItem')->name('transactions.item.update');
    Route::delete('/transactions/item/delete/{item:id}', 'deleteItem')->name('transactions.item.delete');
    Route::post('/transactions/{transaction:uuid}/add/procedure', 'addProcedure')->name('transactions.store.procedure');
    Route::delete('/transactions/procedure/delete/{procedure:id}', 'deleteProcedure')->name('transactions.delete.procedure');
});

Route::controller(TransportController::class)->middleware('auth')->group(function () {
    Route::get('/transport/orders', 'transportOrders')->name('transactions.transportOrders');
    Route::get('/transport/orders/create', 'createTransportOrder')->name('transactions.transportOrders.create');
    Route::post('/transport/orders/store', 'storeTransportOrder')->name('transportOrders.store');
    Route::get('/transport/orders/{transportOrder:uuid}', 'transportOrderDetails')->name('transactions.transportOrders.details');
    Route::get('drivers-and-vehicles', 'driversAndVehicles')->name('relation.drivers.vehicles');
    Route::post('drivers/store', 'storeDriver')->name('relation.driver.store');
    Route::put('drivers/update/{driver:id}', 'updateDriver')->name('relation.driver.update');
    Route::delete('drivers/delete/{driver:id}', 'deleteDriver')->name('relation.driver.delete');
    Route::post('vehicles/store', 'storeVehicle')->name('relation.vehicle.store');
    Route::put('vehicles/update/{vehicle:id}', 'updateVehicle')->name('relation.vehicle.update');
    Route::delete('vehicles/delete/{vehicle:id}', 'deleteVehicle')->name('relation.vehicle.delete');
});

Route::controller(ContractController::class)->middleware('auth')->group(function () {
    Route::get('/contracts', 'contracts')->name('contracts');
    Route::get('/contracts/create', 'createContract')->name('contracts.create');
    Route::post('/contracts/store', 'storeContract')->name('contracts.store');
    Route::get('/contracts/{contract:uuid}', 'contractDetails')->name('contracts.details');
    Route::post('/contracts/update/{contract:uuid}', 'updateContract')->name('contracts.update');
    Route::get('/services', 'services')->name('contracts.services');
    Route::post('/service/store', 'storeService')->name('contracts.service.store');
    Route::put('/service/update/{contract:uuid}', 'updateService')->name('contracts.service.update');
    Route::delete('/contracts/service/delete/{contract:uuid}', 'deleteService')->name('contracts.service.delete');
    Route::post('/contracts/{contract:uuid}/add/attachment', 'attachFile')->name('contracts.add.attachment');
    Route::delete('/contracts/attachment/delete/{attachment:id}', 'deleteAttachment')->name('contracts.delete.attachment');
});

Route::controller(InvoiceController::class)->middleware('auth')->group(function () {
    Route::get('/invoices', 'invoices')->name('invoices');
    Route::get('invoice/create', 'createInvoice')->name('invoices.create');
    Route::post('invoice/create', 'storeInvoice')->name('invoices.store');
    Route::patch('invoice/update/{invoice:uuid}', 'updateInvoice')->name('invoices.update');
    Route::get('/invoice/{invoice:uuid}', 'invoiceDetails')->name('invoices.details');
    Route::post('/invoice/service/store', 'storeServiceInvoice')->name('invoices.service.store');
    Route::get('invoice/services/{invoice:uuid}', 'invoiceServicesDetails')->name('invoices.services.details');
    Route::get('invoice/post/{invoice:uuid}', 'postInvoice')->name('invoices.post');
    Route::post('invoice/clearance/{transaction:uuid}', 'storeClearanceInvoice')->name('invoices.clearance.store');
    Route::get('invoice/clearance/details/{invoice:uuid}', 'clearanceInvoiceDetails')->name('invoices.clearance.details');

    Route::get('/invoices/statements', 'invoiceStatements')->name('invoices.statements');
    Route::get('/invoices/statements/create', 'createInvoiceStatement')->name('invoices.statements.create');
    Route::post('/invoices/statements/store', 'storeInvoiceStatement')->name('invoices.statements.store');
    Route::get('/invoices/statements/{invoiceStatement:uuid}', 'invoiceStatementDetails')->name('invoices.statements.details');
});

Route::controller(AccountingController::class)->middleware('auth')->group(function () {
    Route::get('accounting/tree', 'tree')->name('admin.money.tree');
    Route::post('accounting/tree/create/root', 'createRoot')->name('admin.create.root');
    Route::delete('accounting/tree/delete/{id}', 'deleteRoot')->name('admin.delete.root');
    Route::get('accounting/entries', 'entries')->name('admin.money.entries');
    Route::post('accounting/entries/create/voucher', 'createVoucher')->name('admin.create.voucher');
    Route::delete('accounting/entries/delete/{id}', 'deleteVoucher')->name('admin.delete.voucher');
    Route::get('accounting/voucher/{id}/toJournal', 'convertToJournal')->name('admin.voucher.to.journal');
    Route::post('accounting/entries/create/journal', 'createJournal')->name('admin.create.journal');
    Route::get('accounting/journal/{journal:uuid}', 'journalDetails')->name('admin.journal.details');
    Route::get('accounting/journal/edit/{journal:uuid}', 'editJournal')->name('journal.edit');
    Route::put('accounting/journal/update/{journal:uuid}', 'updateJournal')->name('journal.update');
    Route::get('accounting/reports', 'reports')->name('admin.money.reports');
});

Route::controller(ExportController::class)->group(function () {
    Route::post('/print/{reportType}', 'print')->name('print');
    Route::get('/print/contract/{id}', 'printContract')->name('print.contract');
    Route::get('/print/invoice/{code}', 'printInvoice')->name('print.invoice');
    Route::get('/print/invoice/services/{code}', 'printInvoiceServices')->name('print.invoice.services');
    Route::get('/print/invoice/clearance/{code}', 'printClearanceInvoice')->name('print.invoice.clearance');
    Route::get('/print/invoice/statement/{code}', 'printInvoiceStatement')->name('print.invoice.statement');
    Route::get('/export/excel/{reportType}', 'excel')->name('export.excel');
    Route::get('/export/transport/order/{transportOrder:id}', 'printTransportOrder')->name('export.transport.order');
});