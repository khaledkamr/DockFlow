<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContainerController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CostCenterController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseInvoiceController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\SupplierController;
use App\Models\Container;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\invoice;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Mailer\Transport;

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'loginForm')->name('login.form');
    Route::post('/login', 'login')->name('login');
    Route::post('/logout', 'logout')->name('logout');
});

Route::get('/', [DashboardController::class, 'handle'])->middleware('auth')->name('dashboard');

Route::controller(AdminController::class)->middleware('auth')->group(function() {
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
    Route::post('permissions/store', 'storePermission')->name('admin.permissions.store');
    Route::get('settings', 'settings')->name('settings');
    Route::get('logs', 'logs')->name('admin.logs');
    Route::delete('logs/delete/', 'deleteLogs')->name('admin.logs.delete');
    Route::post('settings/timezone/update', 'updateTimezone')->name('settings.timezone.update');
});

Route::controller(CustomerController::class)->middleware('auth')->group(function () {
    Route::get('/customers', 'customers')->name('relation.customers');
    Route::get('/customer/{customer:uuid}', 'customerProfile')->name('users.customer.profile');
    Route::post('/customer/store', 'storeCustomer')->name('users.customer.store');
    Route::put('/customer/update/{customer:uuid}', 'updateCustomer')->name('users.customer.update');
    Route::delete('/customer/delete/{customer:uuid}', 'deleteCustomer')->name('users.customer.delete');
    Route::get('/customers/{id}/contract', 'getContract')->name('customers.get.contract');
    Route::get('/customers/check-service', 'checkService')->name('customer.check.service');
    Route::get('/api/customers/account/{accountId}/invoices', 'getCustomerInvoicesByAccount')->name('api.customers.invoices.by.account');
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
    Route::delete('/yard/containers/delete/{container:uuid}', 'deleteContainer')->name('containers.delete');
});

Route::controller(PolicyController::class)->middleware('auth')->group(function () {
    Route::get('/policies', 'policies')->name('policies');
    Route::get('/policies/storage/create', 'storagePolicy')->name('policies.storage.create');
    Route::post('/policies/storage/store', 'storeStoragePolicy')->name('policies.storage.store');
    Route::get('/policies/storage/details/{policy:uuid}', 'storagePolicyDetails')->name('policies.storage.details');
    Route::patch('/policies/storage/update/{policy:uuid}', 'updateStoragePolicy')->name('policies.storage.update');

    Route::get('/policies/receive/create', 'createReceivePolicy')->name('policies.receive.create');
    Route::post('/policies/receive/store', 'storeReceivePolicy')->name('policies.receive.store');
    Route::get('/policies/receive/details/{policy:uuid}', 'receivePolicyDetails')->name('policies.receive.details');
    
    Route::get('/policies/services/create', 'servicePolicy')->name('policies.services.create');
    Route::post('/policies/services/store', 'storeServicePolicy')->name('policies.services.store');
    Route::get('/policies/services/details/{policy:uuid}', 'servicePolicyDetails')->name('policies.services.details');

    Route::delete('/policies/delete/{policy:uuid}', 'deletePolicy')->name('policy.delete');
});

Route::controller(TransactionController::class)->middleware('auth')->group(function () {
    Route::get('/transactions', 'transactions')->name('transactions');
    Route::get('/transactions/create', 'createTransaction')->name('transactions.create');
    Route::post('/transactions/store', 'storeTransaction')->name('transactions.store');
    Route::patch('/transactions/update/{transaction:uuid}', 'updateTransaction')->name('transactions.update');
    Route::get('/transactions/{transaction:uuid}', 'transactionDetails')->name('transactions.details');
    Route::post('/transactions/item/store', 'storeItem')->name('transactions.item.store');
    Route::put('/transactions/item/update/{item:id}', 'updateItem')->name('transactions.item.update');
    Route::post('/transactions/item/post/{item:id}', 'postItem')->name('transactions.item.post');
    Route::delete('/transactions/item/delete/{item:id}', 'deleteItem')->name('transactions.item.delete');
    Route::post('/transactions/{transaction:uuid}/add/procedure', 'addProcedure')->name('transactions.store.procedure');
    Route::delete('/transactions/procedure/delete/{procedure:id}', 'deleteProcedure')->name('transactions.delete.procedure');
    Route::get('/transaction/reports', 'reports')->name('transactions.reports');
});

Route::controller(TransportController::class)->middleware('auth')->group(function () {
    Route::get('/transport/orders', 'transportOrders')->name('transactions.transportOrders');
    Route::get('/transport/orders/create', 'createTransportOrder')->name('transactions.transportOrders.create');
    Route::post('/transport/orders/store', 'storeTransportOrder')->name('transportOrders.store');
    Route::patch('/transport/orders/update/{transportOrder:uuid}', 'updateTransportOrder')->name('transportOrders.update');
    Route::patch('/transport/orders/{transportOrder:uuid}/update-notes', 'updateNotes')->name('transportOrders.notes');
    Route::patch('/transport/orders/{transportOrder:uuid}/toggle-receive-status', 'toggleReceiveStatus')->name('transportOrders.toggle'); 
    Route::get('/transport/orders/{transportOrder:uuid}', 'transportOrderDetails')->name('transactions.transportOrders.details');

    Route::get('drivers-and-vehicles', 'driversAndVehicles')->name('relation.drivers.vehicles');

    Route::post('drivers/store', 'storeDriver')->name('relation.driver.store');
    Route::put('drivers/update/{driver:id}', 'updateDriver')->name('relation.driver.update');
    Route::delete('drivers/delete/{driver:id}', 'deleteDriver')->name('relation.driver.delete');

    Route::post('vehicles/store', 'storeVehicle')->name('relation.vehicle.store');
    Route::put('vehicles/update/{vehicle:id}', 'updateVehicle')->name('relation.vehicle.update');
    Route::delete('vehicles/delete/{vehicle:id}', 'deleteVehicle')->name('relation.vehicle.delete');

    Route::post('/destination/store', 'storeDestination')->name('destination.store');
    Route::put('/destination/update/{destination:id}', 'updateDestination')->name('destination.update');
    Route::delete('/destination/delete/{destination:id}', 'deleteDestination')->name('destination.delete');

    Route::get('/transport/reports', 'reports')->name('transactions.transportOrders.reports');
});

Route::controller(ShippingController::class)->middleware('auth')->group(function () {
    Route::get('/shipping/policies', 'policies')->name('shipping.policies');
    Route::get('/shipping/policies/create', 'createPolicy')->name('shipping.policies.create');
    Route::post('/shipping/policies/store', 'storePolicy')->name('shipping.policies.store');
    Route::patch('/shipping/policies/{policy:uuid}/update-notes', 'updateNotes')->name('shipping.policies.notes');
    Route::patch('/shipping/policies/{policy:uuid}/update', 'updatePolicy')->name('shipping.policy.update');
    Route::patch('/shipping/policies/{policy:uuid}/update-goods', 'updateGoods')->name('shipping.policies.goods.update');
    Route::get('/shipping/policies/{policy:uuid}', 'policyDetails')->name('shipping.policies.details');
    Route::patch('/shipping/policies/{policy:uuid}/toggle-receive-status', 'toggleReceiveStatus')->name('shipping.policies.toggle');
    Route::delete('/shipping/policies/delete/{policy:uuid}', 'deletePolicy')->name('shipping.policies.delete');
    Route::get('/shipping/reports', 'reports')->name('shipping.policies.reports');
});

Route::controller(CompanyController::class)->middleware('auth')->group(function () {
    Route::get('/companies', 'companies')->name('companies');
    Route::get('/company/{company:uuid}', 'company')->name('company');
    Route::put('/company/update/{company:uuid}', 'updateCompany')->name('company.update');
    Route::post('/companies/{company:uuid}/add-modules', 'addModuleToCompany')->name('companies.add.modules');
    Route::patch('/companies/{company:uuid}/toggle-module/{moduleId}', 'toggleCompanyModule')->name('companies.toggle.module');
    Route::post('/companies/store-address/{company:uuid}', 'storeAddress')->name('companies.store.address');
    Route::put('/companies/update-address/{company:uuid}', 'updateAddress')->name('companies.update.address');
    Route::post('companies/add-account-number/{company:uuid}', 'storeBankNumber')->name('companies.store.bank');
    Route::put('companies/update-account-number/{bankAccountId}', 'updateBankNumber')->name('companies.update.bank');
    Route::delete('companies/delete-account-number/{bankAccountId}', 'deleteBankNumber')->name('companies.delete.bank');
});

Route::controller(ContractController::class)->middleware('auth')->group(function () {
    Route::get('/contracts', 'contracts')->name('contracts');
    Route::get('/contracts/create', 'createContract')->name('contracts.create');
    Route::post('/contracts/store', 'storeContract')->name('contracts.store');
    Route::patch('/contracts/update/{contract:uuid}', 'updateContract')->name('contracts.update');
    Route::get('/contracts/{contract:uuid}', 'contractDetails')->name('contracts.details');
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
    Route::delete('invoice/delete/{invoice:uuid}', 'deleteInvoice')->name('invoices.delete');

    Route::get('invoice/post/{invoice:uuid}', 'postInvoice')->name('invoices.post');
    Route::get('invoice/post/clearance/{invoice:uuid}', 'postClearanceInvoice')->name('invoices.post.clearance');

    Route::post('/invoice/service/store', 'storeServiceInvoice')->name('invoices.service.store');
    Route::get('invoice/services/{invoice:uuid}', 'invoiceServicesDetails')->name('invoices.services.details');
    
    Route::post('invoice/clearance/{transaction:uuid}', 'storeClearanceInvoice')->name('invoices.clearance.store');
    Route::get('invoice/clearance/details/{invoice:uuid}', 'clearanceInvoiceDetails')->name('invoices.clearance.details');

    Route::get('/invoices/statements', 'invoiceStatements')->name('invoices.statements');
    Route::get('/invoices/statements/create', 'createInvoiceStatement')->name('invoices.statements.create');
    Route::post('/invoices/statements/store', 'storeInvoiceStatement')->name('invoices.statements.store');
    Route::get('/invoices/statements/{invoiceStatement:uuid}', 'invoiceStatementDetails')->name('invoices.statements.details');

    Route::get('/invoices/shipping/create', 'createShippingInvoice')->name('invoices.shipping.create');
    Route::post('/invoices/shipping/store', 'storeShippingInvoice')->name('invoices.shipping.store');
    Route::get('/invoices/shipping/{invoice:uuid}', 'shippingInvoiceDetails')->name('invoices.shipping.details');
    
    Route::get('/invoices/reports', 'invoicesReports')->name('invoices.reports');

    Route::post('/invoices/{invoice:uuid}/add/file', 'attachFile')->name('invoices.add.file');
    Route::delete('/invoices/file/delete/{attachment:id}', 'deleteAttachment')->name('invoices.delete.file');
});

Route::controller(ExpenseInvoiceController::class)->middleware('auth')->group(function () {
    Route::get('/expense/invoices', 'invoices')->name('expense.invoices');
    Route::get('/expense/invoices/create', 'createInvoice')->name('expense.invoices.create');
    Route::post('/expense/invoices/store', 'storeInvoice')->name('expense.invoices.store');
    Route::get('/expense/invoice/{invoice:uuid}', 'invoiceDetails')->name('expense.invoices.details');
    Route::patch('/expense/invoice/update-status/{invoice:uuid}', 'updateInvoiceStatus')->name('expense.invoices.update.status');
    Route::patch('/expense/invoice/update-notes/{invoice:uuid}', 'updateInvoiceNotes')->name('expense.invoices.update.notes');
    Route::get('/expense/invoice/post/{invoice:uuid}', 'postInvoice')->name('expense.invoices.post');
    Route::delete('/expense/invoice/delete/{invoice:uuid}', 'deleteInvoice')->name('expense.invoices.delete');
    Route::get('/expense/invoices/reports', 'reports')->name('expense.invoices.reports');
    Route::post('/expense/invoice/{invoice:uuid}/add/attachment', 'attachFile')->name('expense.invoices.add.attachment');
    Route::delete('/expense/invoice/attachment/delete/{attachment:id}', 'deleteAttachment')->name('expense.invoices.delete.attachment');
});

Route::controller(AccountingController::class)->middleware('auth')->group(function () {
    Route::get('accounting/tree', 'tree')->name('money.tree');
    Route::post('accounting/tree/create/root', 'createRoot')->name('create.root');
    Route::patch('accounting/tree/update/{id}', 'updateRoot')->name('update.root');
    Route::delete('accounting/tree/delete/{id}', 'deleteRoot')->name('delete.root');

    Route::get('accounting/entries', 'entries')->name('money.entries');

    Route::get('accounting/entries/create/voucher-payment', 'createPaymentVoucher')->name('voucher.payment.create');
    Route::get('accounting/entries/create/voucher-receipt', 'createReceiptVoucher')->name('voucher.receipt.create');
    Route::post('accounting/entries/store/voucher', 'storeVoucher')->name('voucher.store');
    Route::get('accounting/entries/{voucher:uuid}', 'voucherDetails')->name('voucher.details');
    Route::get('accounting/entries/print/{voucher:uuid}', 'printVoucher')->name('voucher.print');
    Route::delete('accounting/entries/delete/{id}', 'deleteVoucher')->name('voucher.delete');
    Route::get('accounting/post-voucher/{voucher:uuid}/toJournal', 'postVoucherToJournal')->name('post.voucher');

    Route::get('accounting/entries/create/journal', 'createJournal')->name('money.create.journal');
    Route::post('accounting/entries/create/journal', 'storeJournal')->name('store.journal');
    Route::get('accounting/journal/{journal:uuid}', 'journalDetails')->name('journal.details');
    Route::get('accounting/journal/edit/{journal:uuid}', 'editJournal')->name('journal.edit');
    Route::put('accounting/journal/update/{journal:uuid}', 'updateJournal')->name('journal.update');
    Route::get('accounting/journal/{journal:uuid}/duplicate', 'duplicateJournal')->name('journal.duplicate');
    Route::delete('accounting/journal/delete/{journal:uuid}', 'deleteJournal')->name('journal.delete');

    Route::post('accounting/journal/{journal:uuid}/add/attachment', 'attachFileToJournal')->name('journal.add.attachment');
    Route::delete('accounting/journal/attachment/delete/{attachment:id}', 'deleteJournalAttachment')->name('journal.delete.attachment');

    Route::get('accounting/reports', 'reports')->name('money.reports');
});

Route::controller(CostCenterController::class)->middleware('auth')->group(function () {
    Route::get('accounting/cost-centers', 'costCenters')->name('money.cost.centers');
    Route::post('accounting/cost-centers/store', 'storeCostCenter')->name('cost.centers.store');
    Route::put('accounting/cost-centers/update/{costCenter:id}', 'updateCostCenter')->name('cost.centers.update');
    Route::delete('accounting/cost-centers/delete/{costCenter:id}', 'deleteCostCenter')->name('cost.centers.delete');
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
    Route::get('/export/shipping-policy/{policy:id}', 'printShippingPolicy')->name('export.shipping.policy');
    Route::get('/print/invoice/shipping/{code}', 'printShippingInvoice')->name('print.invoice.shipping');
    Route::get('/print/shipping/reports', 'printShippingReports')->name('print.shipping.reports');
    Route::get('/print/invoices/reports', 'printInvoiceReports')->name('print.invoices.reports');
    Route::get('/print/transaction/reports', 'printTransactionReports')->name('print.transactions.reports');
    Route::get('/print/expense/invoice/{code}', 'printExpenseInvoice')->name('print.expense.invoice');
    Route::get('/print/transport/reports', 'printTransportOrderReports')->name('print.transport.reports');
    Route::get('/print/trial/balance/reports', 'printTrialBalance')->name('print.trial.balance');
});