@extends('layouts.app')

@section('title', 'معاينة ملف المورد - ' . $supplier->name)

@section('content')
    <!-- Supplier Header -->
    <div class="card border-0 rounded-3 shadow-sm mb-3 mb-md-4">
        <div class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
            <div>
                <h4 class="mb-0 fs-5 fs-md-4">
                    <i class="fas fa-building me-2"></i>
                    {{ $supplier->name }}
                    @if ($supplier->account)
                        ({{ $supplier->account->code }})
                    @endif
                    <span class="badge bg-light text-dark rounded-pill">مورد</span>
                </h4>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#editSupplierModal">
                    <i class="fas fa-edit"></i> تعديل بيانات المورد
                </button>              
            </div>
        </div>
        <div class="card-body">
            <div class="row g-2 g-md-3">
                <div class="col-6 col-sm-6 col-lg-4 col-xl">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-receipt text-muted me-2"></i>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">الرقم الضريبي</small>
                            <div class="fw-bold text-truncate">{{ $supplier->vat_number ?? 'غير متوفر' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-lg-4 col-xl">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-id-card text-muted me-2"></i>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">السجل التجاري</small>
                            <div class="fw-bold text-truncate">{{ $supplier->CR ?? 'غير متوفر' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-lg-4 col-xl">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-phone text-muted me-2"></i>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">رقم الهاتف</small>
                            <div class="fw-bold text-truncate">{{ $supplier->phone ?? 'غير متوفر' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-lg-4 col-xl">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-envelope text-muted me-2"></i>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">البريد الإلكتروني</small>
                            <div class="fw-bold text-truncate">{{ $supplier->email ?? 'غير متوفر' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-6 col-lg-4 col-xl">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">العنوان الوطني</small>
                            <div class="fw-bold text-truncate">{{ $supplier->national_address ?? 'غير متوفر' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Supplier Modal -->
    <div class="modal fade" id="editSupplierModal" tabindex="-1"
        aria-labelledby="editSupplierModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white fw-bold"
                        id="editSupplierModalLabel">تعديل بيانات المورد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('users.supplier.update', $supplier) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    <div class="modal-body text-dark">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label">إسم المورد</label>
                                <input type="text" class="form-control border-primary"
                                    id="name" name="name" value="{{ $supplier->name }}">
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="CR" class="form-label">السجل التجاري</label>
                                <input type="text" class="form-control border-primary"
                                    id="CR" name="CR" value="{{ $supplier->CR }}">
                                @error('CR')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="vatNumber" class="form-label">الرقم الضريبي</label>
                                <input type="text" class="form-control border-primary"
                                    id="vatNumber" name="vat_number"
                                    value="{{ $supplier->vat_number }}">
                                @error('vat_number')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="national_address" class="form-label">العنوان الوطني</label>
                                <input type="text" class="form-control border-primary"
                                    id="national_address" name="national_address"
                                    value="{{ $supplier->national_address }}">
                                @error('national_address')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control border-primary"
                                    id="phone" name="phone" value="{{ $supplier->phone }}">
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="text" class="form-control border-primary"
                                    id="email" name="email" value="{{ $supplier->email }}">
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">الحساب الأساسي</label>
                                <select name="account_id" id="account_id">
                                    <option value="" disabled selected>اختر الحساب الأساسي</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ $supplier->account_id == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }} ({{ $account->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">حساب التسوية</label>
                                <select name="settlement_account_id" id="settlement_account_id">
                                    <option value="" disabled selected>اختر حساب التسوية</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" {{ $supplier->settlement_account_id == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }} ({{ $account->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                        <button type="submit" class="btn btn-primary fw-bold order-2 order-sm-1">حفظ التغييرات</button>
                        <button type="button" class="btn btn-secondary fw-bold order-1 order-sm-2"
                            data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Account and Settlement Account Information -->
    <div class="row g-4">
        <div class="col-12 col-md-6">
            <div class="card border-0 rounded-3 shadow-sm mb-3 mb-md-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0 fs-6 fs-md-5">
                        <i class="fas fa-wallet me-2"></i>
                        معلومات الحساب
                    </h5>
                </div>
                <div class="card-body">
                    @if ($supplier->account)
                        <div class="alert alert-primary border-0 rounded-3 mb-0" role="alert">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas fa-info-circle fs-5"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">{{ $supplier->account->name }}</h6>
                                    <p class="mb-0 small">
                                        <strong>الكود:</strong> {{ $supplier->account->code }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-danger border-0 rounded-3" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            لا يوجد حساب أساسي محدد
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card border-0 rounded-3 shadow-sm mb-3 mb-md-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0 fs-6 fs-md-5">
                        <i class="fas fa-balance-scale me-2"></i>
                        حساب التسوية
                    </h5>
                </div>
                <div class="card-body">
                    @if ($supplier->settlementAccount)
                        <div class="alert alert-primary border-0 rounded-3 mb-0" role="alert">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas fa-info-circle fs-5"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">{{ $supplier->settlementAccount->name }}</h6>
                                    <p class="mb-0 small">
                                        <strong>الكود:</strong> {{ $supplier->settlementAccount->code }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-danger border-0 rounded-3" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            لا يوجد حساب تسوية محدد
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Invoices -->
    <div class="card border-0 rounded-3 shadow-sm mb-3 mb-md-4">
        <div class="card-header bg-dark text-white d-flex flex-row justify-content-between align-items-start align-items-md-center gap-2">
            <h5 class="mb-0 fs-6 fs-md-5">
                <i class="fas fa-file-invoice-dollar me-2"></i>
                فواتير المصروفات ({{ count($supplier->expenseInvoices) }})
            </h5>
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-success">
                    مدفوع: {{ collect($supplier->expenseInvoices)->where('is_paid', 1)->count() }}
                </span>
                <span class="badge bg-danger">
                    غير مدفوع: {{ collect($supplier->expenseInvoices)->where('is_paid', 0)->count() }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            @if (count($supplier->expenseInvoices) > 0)
                <div class="table-responsive" id="expenseInvoicesTable">
                    <table class="table table-hover mb-0">
                        <thead class="table-primary text-center">
                            <tr>
                                <th class="text-center fw-bold">#</th>
                                <th class="text-center fw-bold">رقم الفاتورة</th>
                                <th class="text-center fw-bold">رقم فاتورة المورد</th>
                                <th class="text-center fw-bold">التاريخ</th>
                                <th class="text-center fw-bold">المبلغ قبل الضريبة</th>
                                <th class="text-center fw-bold">الضريبة</th>
                                <th class="text-center fw-bold">الإجمالي</th>
                                <th class="text-center fw-bold">طريقة الدفع</th>
                                <th class="text-center fw-bold">حالة الترحيل</th>
                                <th class="text-center fw-bold">حالة الدفع</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($supplier->expenseInvoices as $invoice)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $loop->iteration }}</td>
                                    <td class="fw-bold text-primary">
                                        <a href=""
                                            class="text-decoration-none">
                                            {{ $invoice->code }}
                                        </a>
                                    </td>
                                    <td>{{ $invoice->supplier_invoice_number ?? '-' }}</td>
                                    <td>{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') }}</td>
                                    <td>{{ number_format($invoice->amount_before_tax, 2) }} <i
                                            data-lucide="saudi-riyal"></i></td>
                                    <td>{{ number_format($invoice->tax, 2) }} <i data-lucide="saudi-riyal"></i></td>
                                    <td class="fw-bold">{{ number_format($invoice->total_amount, 2) }} <i
                                            data-lucide="saudi-riyal"></i></td>
                                    <td>{{ $invoice->payment_method }}</td>
                                    <td>
                                        @if ($invoice->is_posted)
                                            <span class="status-success">تم الترحيل</span>
                                        @else
                                            <span class="status-waiting">لم يتم الترحيل</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($invoice->is_paid)
                                            <span class="status-delivered">تم الدفع <i class="fa-solid fa-check"></i></span>
                                        @else
                                            <span class="status-danger">لم يتم الدفع</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                        <div class="card text-center px-3 px-md-5 py-2 border-success flex-grow-1 flex-md-grow-0">
                            <small class="text-success fw-bold">إجمالي المدفوع</small>
                            <div class="fw-bold text-success">
                                {{ number_format(collect($supplier->expenseInvoices)->where('is_paid', 1)->sum('total_amount'), 2) }}
                                <i data-lucide="saudi-riyal"></i>
                            </div>
                        </div>
                        <div class="card text-center px-3 px-md-5 py-2 border-danger flex-grow-1 flex-md-grow-0">
                            <small class="text-danger fw-bold">إجمالي المستحق</small>
                            <div class="fw-bold text-danger">
                                {{ number_format(collect($supplier->expenseInvoices)->where('is_paid', 0)->sum('total_amount'), 2) }}
                                <i data-lucide="saudi-riyal"></i>
                            </div>
                        </div>
                        <div class="card text-center px-3 px-md-5 py-2 border-primary flex-grow-1 flex-md-grow-0">
                            <small class="text-primary fw-bold">إجمالي الفواتير</small>
                            <div class="fw-bold text-primary">
                                {{ number_format(collect($supplier->expenseInvoices)->sum('total_amount'), 2) }}
                                <i data-lucide="saudi-riyal"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-file-invoice fa-3x mb-3"></i>
                    <p>لا توجد فواتير مصروفات لهذا المورد</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Shipping Policies -->
    <div class="card border-0 rounded-3 shadow-sm mb-3 mb-md-4">
        <div
            class="card-header bg-dark text-white d-flex flex-row justify-content-between align-items-start align-items-md-center gap-2">
            <h5 class="mb-0 fs-6 fs-md-5">
                <i class="fas fa-shipping-fast me-2"></i>
                بوالص الشحن ({{ count($supplier->shippingPolicies) }})
            </h5>
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-success">
                    مستلم: {{ collect($supplier->shippingPolicies)->where('is_received', 1)->count() }}
                </span>
                <span class="badge bg-warning text-dark">
                    قيد التنفيذ: {{ collect($supplier->shippingPolicies)->where('is_received', 0)->count() }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            @if (count($supplier->shippingPolicies) > 0)
                <div class="table-responsive" id="shippingPoliciesTable">
                    <table class="table table-hover mb-0">
                        <thead class="table-primary text-center">
                            <tr>
                                <th class="text-center fw-bold">#</th>
                                <th class="text-center fw-bold">رقم البوليصة</th>
                                <th class="text-center fw-bold">النوع</th>
                                <th class="text-center fw-bold">التاريخ</th>
                                <th class="text-center fw-bold">من</th>
                                <th class="text-center fw-bold">إلى</th>
                                <th class="text-center fw-bold">السائق</th>
                                <th class="text-center fw-bold">لوحة المركبة</th>
                                <th class="text-center fw-bold">تكلفة المورد</th>
                                <th class="text-center fw-bold">التكلفة الإجمالية</th>
                                <th class="text-center fw-bold">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($supplier->shippingPolicies as $policy)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $loop->iteration }}</td>
                                    <td class="fw-bold text-primary">
                                        <a href=""
                                            class="text-decoration-none">
                                            {{ $policy->code }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-outline-primary">{{ $policy->type }}</span>
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($policy->date)->format('Y/m/d') }}</td>
                                    <td>{{ $policy->from }}</td>
                                    <td>{{ $policy->to }}</td>
                                    <td>{{ $policy->driver_name ?? '-' }}</td>
                                    <td>{{ $policy->vehicle_plate ?? '-' }}</td>
                                    <td>{{ number_format($policy->supplier_cost, 2) }} <i data-lucide="saudi-riyal"></i>
                                    </td>
                                    <td class="fw-bold">{{ number_format($policy->total_cost, 2) }} <i
                                            data-lucide="saudi-riyal"></i></td>
                                    <td>
                                        @if ($policy->is_received)
                                            <span class="status-delivered">تم الاستلام <i
                                                    class="fa-solid fa-check"></i></span>
                                        @else
                                            <span class="status-waiting">قيد التنفيذ</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                        <div class="card text-center px-3 px-md-5 py-2 border-info flex-grow-1 flex-md-grow-0">
                            <small class="text-info fw-bold">إجمالي تكلفة المورد</small>
                            <div class="fw-bold text-info">
                                {{ number_format(collect($supplier->shippingPolicies)->sum('supplier_cost'), 2) }}
                                <i data-lucide="saudi-riyal"></i>
                            </div>
                        </div>
                        <div class="card text-center px-3 px-md-5 py-2 border-primary flex-grow-1 flex-md-grow-0">
                            <small class="text-primary fw-bold">إجمالي التكلفة</small>
                            <div class="fw-bold text-primary">
                                {{ number_format(collect($supplier->shippingPolicies)->sum('total_cost'), 2) }}
                                <i data-lucide="saudi-riyal"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-truck fa-3x mb-3"></i>
                    <p>لا توجد بوالص شحن لهذا المورد</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Transport Orders -->
    <div class="card border-0 rounded-3 shadow-sm mb-3 mb-md-4">
        <div
            class="card-header bg-dark text-white d-flex flex-row justify-content-between align-items-start align-items-md-center gap-2">
            <h5 class="mb-0 fs-6 fs-md-5">
                <i class="fas fa-truck-loading me-2"></i>
                أوامر النقل ({{ count($supplier->transportOrders) }})
            </h5>
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-success">
                    مستلم: {{ collect($supplier->transportOrders)->where('is_received', 1)->count() }}
                </span>
                <span class="badge bg-warning text-dark">
                    قيد التنفيذ: {{ collect($supplier->transportOrders)->where('is_received', 0)->count() }}
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            @if (count($supplier->transportOrders) > 0)
                <div class="table-responsive" id="transportOrdersTable">
                    <table class="table table-hover mb-0">
                        <thead class="table-primary text-center">
                            <tr>
                                <th class="text-center fw-bold">#</th>
                                <th class="text-center fw-bold">رقم الأمر</th>
                                <th class="text-center fw-bold">النوع</th>
                                <th class="text-center fw-bold">التاريخ</th>
                                <th class="text-center fw-bold">من</th>
                                <th class="text-center fw-bold">إلى</th>
                                <th class="text-center fw-bold">السائق</th>
                                <th class="text-center fw-bold">لوحة المركبة</th>
                                <th class="text-center fw-bold">تكلفة المورد</th>
                                <th class="text-center fw-bold">التكلفة الإجمالية</th>
                                <th class="text-center fw-bold">الحالة</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($supplier->transportOrders as $order)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $loop->iteration }}</td>
                                    <td class="fw-bold text-primary">
                                        <a href=""
                                            class="text-decoration-none">
                                            {{ $order->code }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-outline-primary">{{ $order->type }}</span>
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($order->date)->format('Y/m/d') }}</td>
                                    <td>{{ $order->from }}</td>
                                    <td>{{ $order->to }}</td>
                                    <td>{{ $order->driver_name ?? '-' }}</td>
                                    <td>{{ $order->vehicle_plate ?? '-' }}</td>
                                    <td>{{ number_format($order->supplier_cost, 2) }} <i data-lucide="saudi-riyal"></i>
                                    </td>
                                    <td class="fw-bold">{{ number_format($order->total_cost, 2) }} <i
                                            data-lucide="saudi-riyal"></i></td>
                                    <td>
                                        @if ($order->is_received)
                                            <span class="status-delivered">تم الاستلام <i
                                                    class="fa-solid fa-check"></i></span>
                                        @else
                                            <span class="status-waiting">قيد التنفيذ</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                        <div class="card text-center px-3 px-md-5 py-2 border-info flex-grow-1 flex-md-grow-0">
                            <small class="text-info fw-bold">إجمالي تكلفة المورد</small>
                            <div class="fw-bold text-info">
                                {{ number_format(collect($supplier->transportOrders)->sum('supplier_cost'), 2) }}
                                <i data-lucide="saudi-riyal"></i>
                            </div>
                        </div>
                        <div class="card text-center px-3 px-md-5 py-2 border-primary flex-grow-1 flex-md-grow-0">
                            <small class="text-primary fw-bold">إجمالي التكلفة</small>
                            <div class="fw-bold text-primary">
                                {{ number_format(collect($supplier->transportOrders)->sum('total_cost'), 2) }}
                                <i data-lucide="saudi-riyal"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-truck-loading fa-3x mb-3"></i>
                    <p>لا توجد أوامر نقل لهذا المورد</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        $('#account_id, #settlement_account_id').select2({
            dropdownParent: $('#editSupplierModal'),
            width: '100%'
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Function to check if table needs scrolling
            function checkTableScroll(tableId) {
                const table = document.getElementById(tableId);
                if (table && table.scrollWidth > table.clientWidth) {
                    table.classList.add('has-scroll');
                } else if (table) {
                    table.classList.remove('has-scroll');
                }
            }

            // Check all tables
            checkTableScroll('expenseInvoicesTable');
            checkTableScroll('shippingPoliciesTable');
            checkTableScroll('transportOrdersTable');

            // Re-check on window resize
            window.addEventListener('resize', function() {
                checkTableScroll('expenseInvoicesTable');
                checkTableScroll('shippingPoliciesTable');
                checkTableScroll('transportOrdersTable');
            });

            // Hide scroll hints after first scroll
            const scrollHints = document.querySelectorAll('.scroll-hint');
            const tables = document.querySelectorAll('.table-responsive');

            tables.forEach((table, index) => {
                if (scrollHints[index]) {
                    table.addEventListener('scroll', function() {
                        scrollHints[index].style.display = 'none';
                    }, {
                        once: true
                    });
                }
            });
        });
    </script>
    
    <style>
        .select2-container .select2-selection {
            height: 38px;
            border-radius: 8px;
            border: 1px solid #0d6efd;
            padding: 5px;
        }
        .select2-container .select2-selection__rendered {
            line-height: 30px;
        }
    </style>
@endsection
