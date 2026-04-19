@extends('layouts.app')

@section('title', 'إنشاء فاتورة')

@section('content')
    <style>
        .container-row {
            cursor: pointer;
        }

        .container-row.table-primary {
            background-color: #e7f3ff;
        }

        .checkbox-cell {
            text-align: center;
        }

        .checkbox-cell:hover {
            background-color: #f0f0f0;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        #create-invoice-btn-storage:disabled,
        #create-invoice-btn-shipping:disabled,
        #create-invoice-btn-combined:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .table-container {
            max-height: 500px;
            overflow-y: auto;
            border-radius: 10px;
        }

        .alert {
            animation: slideInDown 0.3s ease;
        }

        mark.bg-warning {
            background-color: #fff3cd !important;
            padding: 2px 4px;
            border-radius: 3px;
        }

        #search-input:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate__pulse {
            animation: pulse 1s infinite;
        }

        .policy-row {
            cursor: pointer;
        }

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

    <div class="mb-4">
        <h1 class="mb-4">إنشاء فاتورة {{ $invoiceType ?? '' }}</h1>
    </div>

    <!-- Invoice Type Selection Step -->
    @if (!isset($invoiceType) || !$invoiceType)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-5">
                <h3 class="mb-4 text-center">اختر نوع الفاتورة</h3>
                <div class="row g-3">
                    @if(auth()->user()->company->hasModule('تخزين'))
                        <div class="col-md-4">
                            <a href="{{ route('invoices.create.unified', ['type' => 'تخزين']) }}"
                                class="btn btn-outline-primary btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4"
                                style="min-height: 200px;">
                                <i class="fas fa-cube fa-3x mb-3"></i>
                                <span class="fw-bold">فاتورة تخزين</span>
                                <small class="mt-2">فاتورة خاصة بتكاليف التخزين</small>
                            </a>
                        </div>
                    @endif
                    @if(auth()->user()->company->hasModule('نقل'))
                        <div class="col-md-4">
                            <a href="{{ route('invoices.create.unified', ['type' => 'شحن']) }}"
                                class="btn btn-outline-success btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4"
                                style="min-height: 200px;">
                                <i class="fas fa-truck fa-3x mb-3"></i>
                                <span class="fw-bold">فاتورة شحن</span>
                                <small class="mt-2">فاتورة خاصة بتكاليف الشحن</small>
                            </a>
                        </div>
                    @endif
                    @if(auth()->user()->company->hasModule('تخزين') && auth()->user()->company->hasModule('نقل'))
                        <div class="col-md-4">
                            <a href="{{ route('invoices.create.unified', ['type' => 'تخزين و شحن']) }}"
                                class="btn btn-outline-info btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4"
                                style="min-height: 200px;">
                                <i class="fas fa-code-merge fa-3x mb-3"></i>
                                <span class="fw-bold">فاتورة مدمجة</span>
                                <small class="mt-2">فاتورة بتكاليف التخزين والشحن معاً</small>
                            </a>
                        </div>
                    @endif
                    @if(auth()->user()->company->hasModule('تخزين'))
                        <div class="col-md-4">
                            <a href="{{ route('invoices.create.unified', ['type' => 'خدمات']) }}"
                                class="btn btn-outline-warning btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4"
                                style="min-height: 200px;">
                                <i class="fas fa-hands-helping fa-3x mb-3"></i>
                                <span class="fw-bold">فاتورة خدمات</span>
                                <small class="mt-2">فاتورة خاصة بتكاليف الخدمات</small>
                            </a>
                        </div>
                    @endif
                    {{-- @if(auth()->user()->company->hasModule('تخليص'))
                        <div class="col-md-4">
                            <a href="{{ route('invoices.create.unified', ['type' => 'تخليص']) }}"
                                class="btn btn-outline-danger btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center p-4"
                                style="min-height: 200px;">
                                <i class="fas fa-ship fa-3x mb-3"></i>
                                <span class="fw-bold">فاتورة تخليص</span>
                                <small class="mt-2">فاتورة خاصة بتكاليف التخليص</small>
                            </a>
                        </div>
                    @endif --}}
                </div>
            </div>
        </div>
    @else
        <!-- Back Button -->
        <div class="mb-3">
            <a href="{{ route('invoices.create.unified') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-right me-2"></i> تغيير نوع الفاتورة
            </a>
        </div>

        <form method="GET" action="" class="mb-4">
            <input type="text" name="type" value="{{ $invoiceType }}" hidden>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <select name="customer_id" id="customer_id" class="form-select border-primary" required>
                        <option value="">-- اختر العميل --</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}"
                                {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->code ?? '' }} - {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <button type="submit" class="btn btn-primary fw-bold w-100">
                        بحث <i class="fa-solid fa-search ms-1"></i>
                    </button>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="input-group">
                        <input id="search-input" class="form-control border-primary" type="search" placeholder="إبحث..."
                            aria-label="Search">
                        <button class="btn btn-outline-primary" type="button" id="clear-search">
                            <i class="fas fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Storage Invoice Section -->
        @if ($invoiceType == 'تخزين' && isset($containers) && $containers->count() > 0)
            <form method="POST" action="{{ route('invoices.store.unified') }}" class="mb-5">
                @csrf
                <input type="hidden" name="type" value="تخزين">
                <input type="hidden" name="customer_id" value="{{ request('customer_id') }}">
                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                <!-- Search results info -->
                <div class="alert alert-secondary mb-3" id="search-info" style="display: none;">
                    <i class="fas fa-search me-1"></i>
                    <span id="search-results-text"></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="clear-search-btn">
                        إلغاء البحث
                    </button>
                </div>

                <!-- Selected containers counter -->
                <div class="alert alert-primary mb-3" id="selection-counter" style="display: none;">
                    <i class="fas fa-info-circle"></i>
                    تم تحديد <strong id="selected-count">0</strong> حاوية
                    بمبلغ إجمالي <strong id="selected-amount">0.00</strong> <i data-lucide="saudi-riyal"></i>
                </div>

                <div class="table-container" id="tableContainer">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" width="50">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select-all-storage">
                                    </div>
                                </th>
                                <th class="text-center">رقم الحاوية</th>
                                <th class="text-center">الحالة</th>
                                <th class="text-center">تاريخ الاستلام</th>
                                <th class="text-center">تاريخ التسليم</th>
                                <th class="text-center">أيام التخزين</th>
                                <th class="text-center">سعر التخزين</th>
                                <th class="text-center">أيام التأخير</th>
                                <th class="text-center">رسوم التأخير</th>
                                <th class="text-center">الخدمات</th>
                                <th class="text-center">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody id="storage-tbody">
                            @foreach ($containers as $container)
                                <tr class="container-row text-center" data-container-id="{{ $container->id }}"
                                    data-amount="{{ $container->total }}">
                                    <td class="checkbox-cell">
                                        <div class="form-check">
                                            <input class="form-check-input container-checkbox" type="checkbox"
                                                name="container_ids[]" value="{{ $container->id }}"
                                                data-amount="{{ $container->total }}">
                                        </div>
                                    </td>
                                    <td class="fw-bold">
                                        <a href="{{ route('container.details', $container) }}"
                                            class="text-decoration-none container-code">
                                            {{ $container->code }}
                                        </a>
                                    </td>
                                    <td><span class="badge status-delivered">{{ $container->status }}</span></td>
                                    <td>{{ Carbon\Carbon::parse($container->date)->format('Y/m/d') }}</td>
                                    <td>{{ Carbon\Carbon::parse($container->exit_date)->format('Y/m/d') }}</td>
                                    <td>{{ $container->storage_days }} يوم</td>
                                    <td>{{ number_format($container->storage_price, 2) }} ر.س</td>
                                    <td>{{ $container->late_days }}</td>
                                    <td>{{ number_format($container->late_fee, 2) }} ر.س</td>
                                    <td>{{ number_format($container->services->sum('pivot.price'), 2) }} ر.س</td>
                                    <td><strong>{{ number_format($container->total, 2) }} ر.س</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- No results message -->
                <div class="alert alert-danger text-center" id="no-results" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i> لا توجد حاويات متطابقة.
                </div>

                <!-- Payment Details Section -->
                <div class="card border-dark mb-3 mt-4" id="payment-details">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">تفاصيل الدفع</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6 col-md">
                                <label for="discount-storage" class="form-label fw-bold">نسبة الخصم (%)</label>
                                <input type="number" class="form-control border-primary" id="discount-storage" name="discount"
                                    min="0" max="100" value="0" step="any">
                            </div>
                            <div class="col-6 col-md">
                                <label for="discount-amount-storage" class="form-label fw-bold">مبلغ الخصم</label>
                                <input type="number" class="form-control border-primary" id="discount-amount-storage" name="discount_amount"
                                    min="0" value="0" step="any">
                            </div>
                            <div class="col-6 col-md">
                                <label for="tax-rate-storage" class="form-label fw-bold">نسبة الضريبة (%)</label>
                                <input type="number" class="form-control border-primary" id="tax-rate-storage" name="tax_rate"
                                    min="0" max="100" value="15" step="any">
                            </div>
                            <div class="col-6 col-md">
                                <label for="payment-method-storage" class="form-label fw-bold">طريقة الدفع</label>
                                <select class="form-select border-primary" id="payment-method-storage" name="payment_method" required>
                                    <option value="آجل">آجل</option>
                                    <option value="كاش">كاش</option>
                                    <option value="تحويل بنكي">تحويل بنكي</option>
                                </select>
                            </div>
                            <div class="col-6 col-md">
                                <label for="invoice-type-storage" class="form-label fw-bold">نوع الفاتورة</label>
                                <select class="form-select border-primary" id="invoice-type-storage" name="invoice_type" required>
                                    <option value="ضريبية">فاتورة ضريبية</option>
                                    <option value="مسودة">فاتورة مسودة</option>
                                </select>
                            </div>
                            <div class="col-6 col-md">
                                <label for="invoice-date-storage" class="form-label fw-bold">تاريخ الفاتورة</label>
                                <input type="date" class="form-control border-primary" id="invoice-date-storage" name="date" 
                                    value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-6 col-md">
                                <div class="p-3 bg-light rounded border border-primary text-center">
                                    <small class="text-muted">المبلغ قبل الخصم</small>
                                    <h6 class="mb-0" id="amount-before-tax-storage">0.00 ر.س</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md">
                                <div class="p-3 bg-light rounded border border-danger text-center">
                                    <small class="text-muted">الخصم</small>
                                    <h6 class="mb-0" id="discount-value-storage">0.00 ر.س</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md">
                                <div class="p-3 bg-light rounded border border-primary text-center">
                                    <small class="text-muted">المبلغ بعد الخصم</small>
                                    <h6 class="mb-0" id="amount-after-discount-storage">0.00 ر.س</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md">
                                <div class="p-3 bg-light rounded border border-primary text-center">
                                    <small class="text-muted">الضريبة المضافة</small>
                                    <h6 class="mb-0" id="tax-amount-storage">0.00 ر.س</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md">
                                <div class="p-3 bg-primary text-white rounded fw-bold text-center">
                                    <small>إجمالي المبلغ</small>
                                    <h6 class="fw-bold mb-0" id="total-amount-storage">0.00 ر.س</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <button type="submit" id="create-invoice-btn-storage" class="btn btn-primary btn-lg" disabled>
                        <i class="fas fa-check me-2"></i> إنشاء الفاتورة
                    </button>
                </div>
            </form>
        @elseif ($invoiceType == 'تخزين' && request('customer_id'))
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                لا توجد حاويات مُسلمة غير مفوترة لهذا العميل.
            </div>
        @endif

        <!-- Shipping Invoice Section -->
        @if ($invoiceType == 'شحن' && isset($shippingPolicies) && $shippingPolicies->count() > 0)
            <form method="POST" action="{{ route('invoices.store.unified') }}" class="mb-5">
                @csrf
                <input type="hidden" name="type" value="شحن">
                <input type="hidden" name="customer_id" value="{{ request('customer_id') }}">
                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                <!-- Search results info -->
                <div class="alert alert-secondary mb-3" id="search-info-shipping" style="display: none;">
                    <i class="fas fa-search me-1"></i>
                    <span id="search-results-text-shipping"></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="clear-search-btn-shipping">
                        إلغاء البحث
                    </button>
                </div>

                <!-- Selected policies counter -->
                <div class="alert alert-primary mb-3" id="selection-counter-shipping" style="display: none;">
                    <i class="fas fa-info-circle"></i>
                    تم تحديد <strong id="selected-count-shipping">0</strong> بوليصة
                    بمبلغ إجمالي <strong id="selected-amount-shipping">0.00</strong> <i data-lucide="saudi-riyal"></i>
                </div>

                <div class="table-container" id="tableContainerShipping">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" width="50">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select-all-shipping">
                                    </div>
                                </th>
                                <th class="text-center">رقم البوليصة</th>
                                <th class="text-center">الحالة</th>
                                <th class="text-center">مكان التحميل</th>
                                <th class="text-center">مكان التفريغ</th>
                                <th class="text-center">التاريخ</th>
                                <th class="text-center">رسوم فسح</th>
                                <th class="text-center">رسوم تأخير</th>
                                <th class="text-center">إجمالي التكلفة</th>
                            </tr>
                        </thead>
                        <tbody id="shipping-tbody">
                            @foreach ($shippingPolicies as $policy)
                                <tr class="policy-row text-center" data-policy-id="{{ $policy->id }}"
                                    data-amount="{{ $policy->total_cost }}">
                                    <td class="checkbox-cell">
                                        <div class="form-check">
                                            <input class="form-check-input policy-checkbox" type="checkbox"
                                                name="shipping_policy_ids[]" value="{{ $policy->id }}"
                                                data-amount="{{ $policy->total_cost }}">
                                        </div>
                                    </td>
                                    <td class="fw-bold">
                                        <a href="{{ route('shipping.policies.details', $policy) }}" class="text-decoration-none policy-code">
                                            {{ $policy->code }}
                                        </a>
                                    </td>
                                    <td><span class="badge status-delivered">{{ $policy->is_received ? 'تم التسليم' : 'قيد النقل' }}</span></td>
                                    <td>{{ $policy->from }}</td>
                                    <td>{{ $policy->to }}</td>
                                    <td>{{ Carbon\Carbon::parse($policy->date)->format('Y/m/d') }}</td>
                                    <td>{{ number_format($policy->clearance_fee, 2) }} ر.س</td>
                                    <td>{{ number_format($policy->late_fee, 2) }} ر.س</td>
                                    <td><strong>{{ number_format($policy->total_cost, 2) }} ر.س</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- No results message -->
                <div class="alert alert-danger text-center" id="no-results-shipping" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i> لا توجد بوليصات متطابقة.
                </div>

                <!-- Payment Details Section -->
                <div class="card border-dark mb-3 mt-4" id="payment-details-shipping">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">تفاصيل الدفع</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6 col-md">
                                <label for="discount-shipping" class="form-label fw-bold">نسبة الخصم (%)</label>
                                <input type="number" class="form-control border-primary" id="discount-shipping" name="discount"
                                    min="0" max="100" value="0" step="any">
                            </div>
                            <div class="col-6 col-md">
                                <label for="discount-amount-shipping" class="form-label fw-bold">مبلغ الخصم</label>
                                <input type="number" class="form-control border-primary" id="discount-amount-shipping" name="discount_amount"
                                    min="0" value="0" step="any">
                            </div>
                            <div class="col-6 col-md">
                                <label for="tax-rate-shipping" class="form-label fw-bold">نسبة الضريبة (%)</label>
                                <input type="number" class="form-control border-primary" id="tax-rate-shipping" name="tax_rate"
                                    min="0" max="100" value="15" step="any">
                            </div>
                            <div class="col-6 col-md">
                                <label for="payment-method-shipping" class="form-label fw-bold">طريقة الدفع</label>
                                <select class="form-select border-primary" id="payment-method-shipping" name="payment_method" required>
                                    <option value="آجل">آجل</option>
                                    <option value="كاش">كاش</option>
                                    <option value="تحويل بنكي">تحويل بنكي</option>
                                </select>
                            </div>
                            <div class="col-6 col-md">
                                <label for="invoice-type-shipping" class="form-label fw-bold">نوع الفاتورة</label>
                                <select class="form-select border-primary" id="invoice-type-shipping" name="invoice_type" required>
                                    <option value="ضريبية">فاتورة ضريبية</option>
                                    <option value="مسودة">فاتورة مسودة</option>
                                </select>
                            </div>
                            <div class="col-6 col-md">
                                <label for="invoice-date-shipping" class="form-label fw-bold">تاريخ الفاتورة</label>
                                <input type="date" class="form-control border-primary" id="invoice-date-shipping" name="date" 
                                    value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-6 col-md">
                                <div class="p-3 bg-light rounded border border-primary text-center">
                                    <small class="text-muted">الإجمالي قبل الخصم</small>
                                    <h6 class="mb-0" id="amount-before-tax-shipping">0.00 ر.س</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md">
                                <div class="p-3 bg-light rounded border border-danger text-center">
                                    <small class="text-muted">الخصم</small>
                                    <h6 class="mb-0" id="discount-value-shipping">0.00 ر.س</h6>
                                </div>
                            </div>
                             <div class="col-6 col-md">
                                <div class="p-3 bg-light rounded border border-primary text-center">
                                    <small class="text-muted">الإجمالي بعد الخصم</small>
                                    <h6 class="mb-0" id="amount-after-discount-shipping">0.00 ر.س</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md">
                                <div class="p-3 bg-light rounded border border-primary text-center">
                                    <small class="text-muted">الضريبة المضافة</small>
                                    <h6 class="mb-0" id="tax-amount-shipping">0.00 ر.س</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md">
                                <div class="p-3 bg-primary text-white rounded fw-bold text-center">
                                    <small>إجمالي المبلغ</small>
                                    <h6 class="fw-bold mb-0" id="total-amount-shipping">0.00 ر.س</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <button type="submit" id="create-invoice-btn-shipping" class="btn btn-primary btn-lg" disabled>
                        <i class="fas fa-check me-2"></i> إنشاء الفاتورة
                    </button>
                </div>
            </form>
        @elseif ($invoiceType == 'شحن' && request('customer_id'))
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                لا توجد بوالص شحن مُستلمة غير مفوترة لهذا العميل.
            </div>
        @endif

        <!-- Combined Invoice Section -->
        @if ($invoiceType == 'تخزين و شحن' && ((isset($containers) && $containers->count() > 0) ||
                (isset($shippingPolicies) && $shippingPolicies->count() > 0)))
            <form method="POST" action="{{ route('invoices.store.unified') }}" class="mb-5">
                @csrf
                <input type="hidden" name="type" value="تخزين و شحن">
                <input type="hidden" name="customer_id" value="{{ request('customer_id') }}">
                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                <!-- Storage Section -->
                @if (isset($containers) && $containers->count() > 0)
                    <h6 class="fw-bold mb-2">الحاويات المسلمة:</h6>
                    <div class="mb-3">
                        <!-- Search results info -->
                        <div class="alert alert-secondary mb-3" id="search-info-storage" style="display: none;">
                            <i class="fas fa-search me-1"></i>
                            <span id="search-results-text-storage"></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                                id="clear-search-btn-storage">
                                إلغاء البحث
                            </button>
                        </div>

                        <!-- Selected containers counter -->
                        <div class="alert alert-primary mb-3" id="selection-counter-storage" style="display: none;">
                            <i class="fas fa-info-circle"></i>
                            تم تحديد <strong id="selected-count-storage">0</strong> حاوية
                            بمبلغ إجمالي <strong id="selected-amount-storage">0.00</strong> <i data-lucide="saudi-riyal"></i>
                        </div>

                        <div class="table-container" id="tableContainerStorageCombined">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center" width="50">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="select-all-storage-combined">
                                            </div>
                                        </th>
                                        <th class="text-center">رقم الحاوية</th>
                                        <th class="text-center">الحالة</th>
                                        <th class="text-center">تاريخ الاستلام</th>
                                        <th class="text-center">تاريخ التسليم</th>
                                        <th class="text-center">أيام التخزين</th>
                                        <th class="text-center">سعر التخزين</th>
                                        <th class="text-center">أيام التأخير</th>
                                        <th class="text-center">رسوم التأخير</th>
                                        <th class="text-center">الخدمات</th>
                                        <th class="text-center">الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody id="storage-tbody-combined">
                                    @foreach ($containers as $container)
                                        <tr class="container-row text-center" data-container-id="{{ $container->id }}"
                                            data-amount="{{ $container->total }}">
                                            <td class="checkbox-cell">
                                                <div class="form-check">
                                                    <input class="form-check-input container-checkbox-combined"
                                                        type="checkbox" name="container_ids[]"
                                                        value="{{ $container->id }}"
                                                        data-amount="{{ $container->total }}">
                                                </div>
                                            </td>
                                            <td class="fw-bold">
                                                <a href="{{ route('container.details', $container) }}"
                                                    class="text-decoration-none container-code">
                                                    {{ $container->code }}
                                                </a>
                                            </td>
                                            <td><span class="badge status-delivered">{{ $container->status }}</span></td>
                                            <td>{{ Carbon\Carbon::parse($container->date)->format('Y/m/d') }}</td>
                                            <td>{{ Carbon\Carbon::parse($container->exit_date)->format('Y/m/d') }}</td>
                                            <td>{{ $container->storage_days }} يوم</td>
                                            <td>{{ number_format($container->storage_price, 2) }} ر.س</td>
                                            <td>{{ $container->late_days }}</td>
                                            <td>{{ number_format($container->late_fee, 2) }} ر.س</td>
                                            <td>{{ number_format($container->services->sum('pivot.price'), 2) }} ر.س</td>
                                            <td><strong>{{ number_format($container->total, 2) }} ر.س</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- No results message -->
                        <div class="alert alert-danger text-center" id="no-results-storage" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i> لا توجد حاويات متطابقة.
                        </div>
                    </div>
                @endif

                <!-- Shipping Section -->
                @if (isset($shippingPolicies) && $shippingPolicies->count() > 0)
                    <h6 class="fw-bold mb-2">بوالص الشحن:</h6>
                    <div class="mb-3">
                        <!-- Search results info -->
                        <div class="alert alert-secondary mb-3" id="search-info-shipping-combined"
                            style="display: none;">
                            <i class="fas fa-search me-1"></i>
                            <span id="search-results-text-shipping-combined"></span>
                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2"
                                id="clear-search-btn-shipping-combined">
                                إلغاء البحث
                            </button>
                        </div>

                        <!-- Selected policies counter -->
                        <div class="alert alert-primary mb-3" id="selection-counter-shipping-combined"
                            style="display: none;">
                            <i class="fas fa-info-circle"></i>
                            تم تحديد <strong id="selected-count-shipping-combined">0</strong> بوليصة
                            بمبلغ إجمالي <strong id="selected-amount-shipping-combined">0.00</strong> <i data-lucide="saudi-riyal"></i>
                        </div>

                        <div class="table-container" id="tableContainerShippingCombined">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center" width="50">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="select-all-shipping-combined">
                                            </div>
                                        </th>
                                        <th class="text-center">رقم البوليصة</th>
                                        <th class="text-center">الحالة</th>
                                        <th class="text-center">مكان التحميل</th>
                                        <th class="text-center">مكان التفريغ</th>
                                        <th class="text-center">التاريخ</th>
                                        <th class="text-center">رسوم فسح</th>
                                        <th class="text-center">رسوم تأخير</th>
                                        <th class="text-center">إجمالي التكلفة</th>
                                    </tr>
                                </thead>
                                <tbody id="shipping-tbody-combined">
                                    @foreach ($shippingPolicies as $policy)
                                        <tr class="policy-row text-center" data-policy-id="{{ $policy->id }}"
                                            data-amount="{{ $policy->total_cost }}">
                                            <td class="checkbox-cell">
                                                <div class="form-check">
                                                    <input class="form-check-input policy-checkbox-combined"
                                                        type="checkbox" name="shipping_policy_ids[]"
                                                        value="{{ $policy->id }}"
                                                        data-amount="{{ $policy->total_cost }}">
                                                </div>
                                            </td>
                                            <td class="fw-bold">
                                                <a href="{{ route('shipping.policies.details', $policy) }}" class="text-decoration-none policy-code">
                                                    {{ $policy->code }}
                                                </a>
                                            </td>
                                            <td><span class="badge status-delivered">{{ $policy->is_received ? 'تم التسليم' : 'قيد النقل' }}</span></td>
                                            <td>{{ $policy->from }}</td>
                                            <td>{{ $policy->to }}</td>
                                            <td>{{ Carbon\Carbon::parse($policy->date)->format('Y/m/d') }}</td>
                                            <td>{{ number_format($policy->clearance_fee, 2) }} ر.س</td>
                                            <td>{{ number_format($policy->late_fee, 2) }} ر.س</td>
                                            <td><strong>{{ number_format($policy->total_cost, 2) }} ر.س</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- No results message -->
                        <div class="alert alert-danger text-center" id="no-results-shipping-combined"
                            style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i> لا توجد بوليصات متطابقة.
                        </div>
                    </div>
                @endif

                <!-- Payment Details Section -->
                <div class="card border-dark mb-3 mt-4" id="payment-details-combined">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">تفاصيل الدفع</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6 col-md">
                                <label for="discount-combined" class="form-label fw-bold">نسبة الخصم (%)</label>
                                <input type="number" class="form-control border-primary" id="discount-combined" name="discount"
                                    min="0" max="100" value="0" step="any">
                            </div>
                            <div class="col-6 col-md">
                                <label for="discount-amount-combined" class="form-label fw-bold">مبلغ الخصم</label>
                                <input type="number" class="form-control border-primary" id="discount-amount-combined" name="discount_amount"
                                    min="0" value="0" step="any">
                            </div>
                            <div class="col-6 col-md">
                                <label for="tax-rate-combined" class="form-label fw-bold">نسبة الضريبة (%)</label>
                                <input type="number" class="form-control border-primary" id="tax-rate-combined" name="tax_rate"
                                    min="0" max="100" value="15" step="0.01">
                            </div>
                            <div class="col-6 col-md">
                                <label for="payment-method-combined" class="form-label fw-bold">طريقة الدفع</label>
                                <select class="form-select border-primary" id="payment-method-combined" name="payment_method" required>
                                    <option value="آجل">آجل</option>
                                    <option value="كاش">كاش</option>
                                    <option value="تحويل بنكي">تحويل بنكي</option>
                                </select>
                            </div>
                            <div class="col-6 col-md">
                                <label for="invoice-type-combined" class="form-label fw-bold">نوع الفاتورة</label>
                                <select class="form-select border-primary" id="invoice-type-combined" name="invoice_type" required>
                                    <option value="ضريبية">فاتورة ضريبية</option>
                                    <option value="مسودة">فاتورة مسودة</option>
                                </select>
                            </div>
                            <div class="col-6 col-md">
                                <label for="invoice-date-combined" class="form-label fw-bold">تاريخ الفاتورة</label>
                                <input type="date" class="form-control border-primary" id="invoice-date-combined" name="date" 
                                    value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-12 col-md">
                                <div class="p-3 bg-light rounded border border-primary text-center">
                                    <small class="text-muted">المبلغ قبل الخصم</small>
                                    <h6 class="mb-0" id="amount-before-tax-combined">0.00 ر.س</h6>
                                </div>
                            </div>
                            <div class="col-12 col-md">
                                <div class="p-3 bg-light rounded border border-danger text-center">
                                    <small class="text-muted">الخصم</small>
                                    <h6 class="mb-0" id="discount-value-combined">0.00 ر.س</h6>
                                </div>
                            </div>
                            <div class="col-12 col-md">
                                <div class="p-3 bg-light rounded border border-primary text-center">
                                    <small class="text-muted">المبلغ بعد الخصم</small>
                                    <h6 class="mb-0" id="amount-after-discount-combined">0.00 ر.س</h6>
                                </div>
                            </div>
                            <div class="col-12 col-md">
                                <div class="p-3 bg-light rounded border border-primary text-center">
                                    <small class="text-muted">الضريبة المضافة</small>
                                    <h6 class="mb-0" id="tax-amount-combined">0.00 ر.س</h6>
                                </div>
                            </div>
                            <div class="col-12 col-md">
                                <div class="p-3 bg-primary text-white rounded fw-bold text-center">
                                    <small>إجمالي المبلغ</small>
                                    <h6 class="mb-0 fw-bold" id="total-amount-combined">0.00 ر.س</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <button type="submit" id="create-invoice-btn-combined" class="btn btn-primary btn-lg" disabled>
                        <i class="fas fa-check me-2"></i> إنشاء الفاتورة
                    </button>
                </div>
            </form>
        @elseif ($invoiceType == 'تخزين و شحن' && request('customer_id'))
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                لا توجد حاويات أو بوالص شحن غير مفوترة لهذا العميل.
            </div>
        @endif

        @if($invoiceType == 'خدمات' && isset($servicePolicies) && $servicePolicies->count() > 0)
            <form method="POST" action="{{ route('invoices.store.unified') }}" class="mb-5">
                @csrf
                <input type="hidden" name="type" value="خدمات">
                <input type="hidden" name="customer_id" value="{{ request('customer_id') }}">
                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                <!-- Search results info -->
                <div class="alert alert-secondary mb-3" id="search-info-services" style="display: none;">
                    <i class="fas fa-search me-1"></i>
                    <span id="search-results-text-services"></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" id="clear-search-btn-services">
                        إلغاء البحث
                    </button>
                </div>

                <!-- Selected policies counter -->
                <div class="alert alert-primary mb-3" id="selection-counter-services" style="display: none;">
                    <i class="fas fa-info-circle"></i>
                    تم تحديد <strong id="selected-count-services">0</strong> بوليصة
                    بمبلغ إجمالي <strong id="selected-amount-services">0.00</strong> <i data-lucide="saudi-riyal"></i>
                </div>

                <div class="table-container" id="tableContainerServices">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" width="50">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select-all-services">
                                    </div>
                                </th>
                                <th class="text-center">رقم البوليصة</th>
                                <th class="text-center">رقم الحاوية</th>
                                <th class="text-center">نوع الحاوية</th>
                                <th class="text-center">التاريخ</th>
                                <th class="text-center">الخدمة</th>
                                <th class="text-center">التكلفة</th>
                            </tr>
                        </thead>
                        <tbody id="services-tbody">
                            @foreach ($servicePolicies as $policy)
                                <tr class="policy-row text-center" data-policy-id="{{ $policy->id }}" 
                                    data-amount="{{ $policy->containers->first()->services->sum('pivot.price') }}">
                                    <td class="checkbox-cell">
                                        <div class="form-check">
                                            <input class="form-check-input service-checkbox" type="checkbox"
                                                name="container_ids[]" value="{{ $policy->containers->first()->id }}"
                                                data-amount="{{ $policy->containers->first()->services->sum('pivot.price') }}">
                                        </div>
                                    </td>
                                    <td class="fw-bold">
                                        <a href="{{ route('policies.services.details', $policy) }}" class="text-decoration-none policy-code text-dark">
                                            {{ $policy->code }}
                                        </a>
                                    </td>
                                    <td class="fw-bold">
                                        <a href="{{ route('container.details', $policy->containers->first()) }}" class="text-decoration-none policy-code text-dark">
                                            {{ $policy->containers->first()->code }}
                                        </a>
                                    </td>
                                    <td>{{ $policy->containers->first()->containerType->name }}</td>
                                    <td>{{ Carbon\Carbon::parse($policy->containers->first()->date)->format('Y/m/d') }}</td>
                                    <td>{{ $policy->containers->first()->services->first()->description }}</td>
                                    <td class="fw-bold">{{ $policy->containers->first()->services->first()->pivot->price }} <i data-lucide="saudi-riyal"></i></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- No results message -->
                <div class="alert alert-danger text-center" id="no-results-services" style="display: none;">
                    <i class="fas fa-exclamation-triangle me-2"></i> لا توجد بوليصات متطابقة.
                </div>

                <!-- Payment Details Section -->
                <div class="card border-dark mb-3 mt-4" id="payment-details-services">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">تفاصيل الدفع</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6 col-md">
                                <label for="discount-services" class="form-label fw-bold">نسبة الخصم (%)</label>
                                <input type="number" class="form-control border-primary" id="discount-services" name="discount"
                                    min="0" max="100" value="0" step="any">
                            </div>
                            <div class="col-6 col-md">
                                <label for="discount-amount-services" class="form-label fw-bold">مبلغ الخصم</label>
                                <input type="number" class="form-control border-primary" id="discount-amount-services" name="discount_amount"
                                    min="0" value="0" step="any">
                            </div>
                            <div class="col-6 col-md">
                                <label for="tax-rate-services" class="form-label fw-bold">نسبة الضريبة (%)</label>
                                <input type="number" class="form-control border-primary" id="tax-rate-services" name="tax_rate"
                                    min="0" max="100" value="15" step="any">
                            </div>
                            <div class="col-6 col-md">
                                <label for="payment-method-services" class="form-label fw-bold">طريقة الدفع</label>
                                <select class="form-select border-primary" id="payment-method-services" name="payment_method" required>
                                    <option value="آجل">آجل</option>
                                    <option value="كاش">كاش</option>
                                    <option value="تحويل بنكي">تحويل بنكي</option>
                                </select>
                            </div>
                            <div class="col-6 col-md">
                                <label for="invoice-type-services" class="form-label fw-bold">نوع الفاتورة</label>
                                <select class="form-select border-primary" id="invoice-type-services" name="invoice_type" required>
                                    <option value="ضريبية">فاتورة ضريبية</option>
                                    <option value="مسودة">فاتورة مسودة</option>
                                </select>
                            </div>
                            <div class="col-6 col-md">
                                <label for="invoice-date-services" class="form-label fw-bold">تاريخ الفاتورة</label>
                                <input type="date" class="form-control border-primary" id="invoice-date-services" name="date" 
                                    value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-6 col-md">
                                <div class="p-3 bg-light rounded border border-primary text-center">
                                    <small class="text-muted">الإجمالي قبل الخصم</small>
                                    <h6 class="mb-0" id="amount-before-tax-services">0.00 ر.س</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md">
                                <div class="p-3 bg-light rounded border border-danger text-center">
                                    <small class="text-muted">الخصم</small>
                                    <h6 class="mb-0" id="discount-value-services">0.00 ر.س</h6>
                                </div>
                            </div>
                             <div class="col-6 col-md">
                                <div class="p-3 bg-light rounded border border-primary text-center">
                                    <small class="text-muted">الإجمالي بعد الخصم</small>
                                    <h6 class="mb-0" id="amount-after-discount-services">0.00 ر.س</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md">
                                <div class="p-3 bg-light rounded border border-primary text-center">
                                    <small class="text-muted">الضريبة المضافة</small>
                                    <h6 class="mb-0" id="tax-amount-services">0.00 ر.س</h6>
                                </div>
                            </div>
                            <div class="col-6 col-md">
                                <div class="p-3 bg-primary text-white rounded fw-bold text-center">
                                    <small>إجمالي المبلغ</small>
                                    <h6 class="fw-bold mb-0" id="total-amount-services">0.00 ر.س</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <button type="submit" id="create-invoice-btn-services" class="btn btn-primary btn-lg" disabled>
                        <i class="fas fa-check me-2"></i> إنشاء الفاتورة
                    </button>
                </div>
            </form>
        @elseif($invoiceType == 'خدمات' && request('customer_id'))
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                لا توجد خدمات غير مفوترة لهذا العميل.
            </div>
        @endif
    @endif

    <script>
        $("#customer_id").select2({
            language: {
                noResults: function() {
                    return "لم يتم العثور على نتائج";
                }
            },
            width: '100%'
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Storage Invoice Functions
            const storageCheckboxes = document.querySelectorAll('.container-checkbox');
            const storageSelectAll = document.getElementById('select-all-storage');
            const storageCombinedCheckboxes = document.querySelectorAll('.container-checkbox-combined');
            const storageCombinedSelectAll = document.getElementById('select-all-storage-combined');

            // Shipping Invoice Functions
            const shippingCheckboxes = document.querySelectorAll('.policy-checkbox');
            const shippingSelectAll = document.getElementById('select-all-shipping');
            const shippingCombinedCheckboxes = document.querySelectorAll('.policy-checkbox-combined');
            const shippingCombinedSelectAll = document.getElementById('select-all-shipping-combined');

            // Service Invoice Functions
            const serviceCheckboxes = document.querySelectorAll('.service-checkbox');
            const serviceSelectAll = document.getElementById('select-all-services');

            // Search functionality
            const searchInput = document.getElementById('search-input');
            const clearSearch = document.getElementById('clear-search');

            function calculateTotals(checkboxes, prefix) {
                let total = 0;
                checkboxes.forEach(cb => {
                    if (cb.checked) {
                        total += parseFloat(cb.dataset.amount) || 0;
                    }
                });
                return total;
            }

            function updateUI() {
                const invoiceType = '{{ $invoiceType ?? null }}';

                if (invoiceType === 'تخزين') {
                    updateStorageUI();
                } else if (invoiceType === 'شحن') {
                    updateShippingUI();
                } else if (invoiceType === 'تخزين و شحن') {
                    updateCombinedUI();
                } else if (invoiceType === 'خدمات') {
                    updateServicesUI();
                }
            }

            function updateStorageUI() {
                const discountInput = document.getElementById('discount-storage');
                const discountAmountInput = document.getElementById('discount-amount-storage');
                const amountBeforeTax = calculateTotals(storageCheckboxes, 'storage');
                const taxRate = parseFloat(document.getElementById('tax-rate-storage')?.value || 0);

                discountInput.addEventListener('input', function() {
                    const inputValue = parseFloat(this.value) || 0;
                    if (!isNaN(inputValue) && inputValue >= 0) {
                        const discountValue = (inputValue / 100) * amountBeforeTax;
                        discountAmountInput.value = discountValue.toFixed(2);
                    }
                    updateUI();
                });

                discountAmountInput.addEventListener('input', function() {
                    const inputValue = parseFloat(this.value) || 0;
                    if (amountBeforeTax > 0 && !isNaN(inputValue) && inputValue >= 0) {
                        const discountPercent = (inputValue / amountBeforeTax) * 100;
                        discountInput.value = Math.min(discountPercent, 100).toFixed(2);
                    }
                    updateUI();
                });
                
                const discountValue = parseFloat(discountAmountInput.value || 0);
                const amountAfterDiscount = amountBeforeTax - discountValue;
                const tax = (taxRate / 100) * amountAfterDiscount;
                const total = amountAfterDiscount + tax;

                document.getElementById('amount-before-tax-storage').textContent = amountBeforeTax.toFixed(2) + ' ر.س';
                document.getElementById('discount-value-storage').textContent = discountValue.toFixed(2) + ' ر.س';
                document.getElementById('amount-after-discount-storage').textContent = amountAfterDiscount.toFixed(2) + ' ر.س';
                document.getElementById('tax-amount-storage').textContent = tax.toFixed(2) + ' ر.س';
                document.getElementById('total-amount-storage').textContent = total.toFixed(2) + ' ر.س';

                const counter = document.getElementById('selection-counter');
                const selectedCount = Array.from(storageCheckboxes).filter(cb => cb.checked).length;

                if (selectedCount > 0) {
                    document.getElementById('selected-count').textContent = selectedCount;
                    document.getElementById('selected-amount').textContent = amountBeforeTax.toFixed(2);
                    counter.style.display = 'block';
                    document.getElementById('create-invoice-btn-storage').disabled = false;
                } else {
                    counter.style.display = 'none';
                    document.getElementById('create-invoice-btn-storage').disabled = true;
                }
            }

            function updateShippingUI() {
                const discountInput = document.getElementById('discount-shipping');
                const discountAmountInput = document.getElementById('discount-amount-shipping');
                const amountBeforeTax = calculateTotals(shippingCheckboxes, 'shipping');
                const taxRate = parseFloat(document.getElementById('tax-rate-shipping')?.value || 0);

                discountInput.addEventListener('input', function() {
                    const inputValue = parseFloat(this.value) || 0;
                    if (!isNaN(inputValue) && inputValue >= 0) {
                        const discountValue = (inputValue / 100) * amountBeforeTax;
                        discountAmountInput.value = discountValue.toFixed(2);
                    }
                    updateUI();
                });

                discountAmountInput.addEventListener('input', function() {
                    const inputValue = parseFloat(this.value) || 0;
                    if (amountBeforeTax > 0 && !isNaN(inputValue) && inputValue >= 0) {
                        const discountPercent = (inputValue / amountBeforeTax) * 100;
                        discountInput.value = Math.min(discountPercent, 100).toFixed(2);
                    }
                    updateUI();
                });

                const discountValue = parseFloat(discountAmountInput.value || 0);
                const amountAfterDiscount = amountBeforeTax - discountValue;
                const tax = (taxRate / 100) * amountAfterDiscount;
                const total = amountAfterDiscount + tax;

                document.getElementById('amount-before-tax-shipping').textContent = amountBeforeTax.toFixed(2) +' ر.س';
                document.getElementById('discount-value-shipping').textContent = discountValue.toFixed(2) + ' ر.س';
                document.getElementById('amount-after-discount-shipping').textContent = amountAfterDiscount.toFixed(2) + ' ر.س';
                document.getElementById('tax-amount-shipping').textContent = tax.toFixed(2) + ' ر.س';
                document.getElementById('total-amount-shipping').textContent = total.toFixed(2) + ' ر.س';

                const counter = document.getElementById('selection-counter-shipping');
                const selectedCount = Array.from(shippingCheckboxes).filter(cb => cb.checked).length;

                if (selectedCount > 0) {
                    document.getElementById('selected-count-shipping').textContent = selectedCount;
                    document.getElementById('selected-amount-shipping').textContent = amountBeforeTax.toFixed(2);
                    counter.style.display = 'block';
                    document.getElementById('create-invoice-btn-shipping').disabled = false;
                } else {
                    counter.style.display = 'none';
                    document.getElementById('create-invoice-btn-shipping').disabled = true;
                }
            }

            function updateCombinedUI() {
                const storageTotal = calculateTotals(storageCombinedCheckboxes, 'storage');
                const shippingTotal = calculateTotals(shippingCombinedCheckboxes, 'shipping');
                const discountInput = document.getElementById('discount-combined');
                const discountAmountInput = document.getElementById('discount-amount-combined');
                const amountBeforeTax = storageTotal + shippingTotal;
                const taxRate = parseFloat(document.getElementById('tax-rate-combined')?.value || 0);

                discountInput.addEventListener('input', function() {
                    const inputValue = parseFloat(this.value) || 0;
                    if (!isNaN(inputValue) && inputValue >= 0) {
                        const discountValue = (inputValue / 100) * amountBeforeTax;
                        discountAmountInput.value = discountValue.toFixed(2);
                    }
                    updateUI();
                });

                discountAmountInput.addEventListener('input', function() {
                    const inputValue = parseFloat(this.value) || 0;
                    if (amountBeforeTax > 0 && !isNaN(inputValue) && inputValue >= 0) {
                        const discountPercent = (inputValue / amountBeforeTax) * 100;
                        discountInput.value = Math.min(discountPercent, 100).toFixed(2);
                    }
                    updateUI();
                });

                const discountValue = parseFloat(discountAmountInput.value || 0);
                const amountAfterDiscount = amountBeforeTax - discountValue;
                const tax = (taxRate / 100) * amountAfterDiscount;
                const total = amountAfterDiscount + tax;

                document.getElementById('amount-before-tax-combined').textContent = amountBeforeTax.toFixed(2) + ' ر.س';
                document.getElementById('discount-value-combined').textContent = discountValue.toFixed(2) + ' ر.س';
                document.getElementById('amount-after-discount-combined').textContent = amountAfterDiscount.toFixed(2) + ' ر.س';
                document.getElementById('tax-amount-combined').textContent = tax.toFixed(2) + ' ر.س';
                document.getElementById('total-amount-combined').textContent = total.toFixed(2) + ' ر.س';

                const storageCb = Array.from(storageCombinedCheckboxes).filter(cb => cb.checked).length;
                const shippingCb = Array.from(shippingCombinedCheckboxes).filter(cb => cb.checked).length;

                if (storageCb > 0) {
                    document.getElementById('selected-count-storage').textContent = storageCb;
                    document.getElementById('selected-amount-storage').textContent = storageTotal.toFixed(2);
                    document.getElementById('selection-counter-storage').style.display = 'block';
                } else {
                    document.getElementById('selection-counter-storage').style.display = 'none';
                }

                if (shippingCb > 0) {
                    document.getElementById('selected-count-shipping-combined').textContent = shippingCb;
                    document.getElementById('selected-amount-shipping-combined').textContent = shippingTotal.toFixed(2);
                    document.getElementById('selection-counter-shipping-combined').style.display = 'block';
                } else {
                    document.getElementById('selection-counter-shipping-combined').style.display = 'none';
                }

                if (storageCb > 0 || shippingCb > 0) {
                    document.getElementById('create-invoice-btn-combined').disabled = false;
                } else {
                    document.getElementById('create-invoice-btn-combined').disabled = true;
                }
            }

            function updateServicesUI() {
                const discountInput = document.getElementById('discount-services');
                const discountAmountInput = document.getElementById('discount-amount-services');
                const amountBeforeTax = calculateTotals(serviceCheckboxes, 'services');
                const taxRate = parseFloat(document.getElementById('tax-rate-services')?.value || 0);

                discountInput.addEventListener('input', function() {
                    const inputValue = parseFloat(this.value) || 0;
                    if (!isNaN(inputValue) && inputValue >= 0) {
                        const discountValue = (inputValue / 100) * amountBeforeTax;
                        discountAmountInput.value = discountValue.toFixed(2);
                    }
                    updateUI();
                });

                discountAmountInput.addEventListener('input', function() {
                    const inputValue = parseFloat(this.value) || 0;
                    if (amountBeforeTax > 0 && !isNaN(inputValue) && inputValue >= 0) {
                        const discountPercent = (inputValue / amountBeforeTax) * 100;
                        discountInput.value = Math.min(discountPercent, 100).toFixed(2);
                    }
                    updateUI();
                });

                const discountValue = parseFloat(discountAmountInput.value || 0);
                const amountAfterDiscount = amountBeforeTax - discountValue;
                const tax = (taxRate / 100) * amountAfterDiscount;
                const total = amountAfterDiscount + tax;

                document.getElementById('amount-before-tax-services').textContent = amountBeforeTax.toFixed(2) + ' ر.س';
                document.getElementById('discount-value-services').textContent = discountValue.toFixed(2) + ' ر.س';
                document.getElementById('amount-after-discount-services').textContent = amountAfterDiscount.toFixed(2) + ' ر.س';
                document.getElementById('tax-amount-services').textContent = tax.toFixed(2) + ' ر.س';
                document.getElementById('total-amount-services').textContent = total.toFixed(2) + ' ر.س';

                const counter = document.getElementById('selection-counter-services');
                const selectedCount = Array.from(serviceCheckboxes).filter(cb => cb.checked).length;

                if (selectedCount > 0) {
                    document.getElementById('selected-count-services').textContent = selectedCount;
                    document.getElementById('selected-amount-services').textContent = amountBeforeTax.toFixed(2);
                    counter.style.display = 'block';
                    document.getElementById('create-invoice-btn-services').disabled = false;
                } else {
                    counter.style.display = 'none';
                    document.getElementById('create-invoice-btn-services').disabled = true;
                }
            }

            // Event listeners for Storage
            storageCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateUI);
            });
            if (storageSelectAll) {
                storageSelectAll.addEventListener('change', function() {
                    storageCheckboxes.forEach(cb => cb.checked = this.checked);
                    updateUI();
                });
            }

            // Event listeners for Shipping
            shippingCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateUI);
            });
            if (shippingSelectAll) {
                shippingSelectAll.addEventListener('change', function() {
                    shippingCheckboxes.forEach(cb => cb.checked = this.checked);
                    updateUI();
                });
            }

            // Event listeners for Combined Storage
            storageCombinedCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateUI);
            });
            if (storageCombinedSelectAll) {
                storageCombinedSelectAll.addEventListener('change', function() {
                    storageCombinedCheckboxes.forEach(cb => cb.checked = this.checked);
                    updateUI();
                });
            }

            // Event listeners for Combined Shipping
            shippingCombinedCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateUI);
            });
            if (shippingCombinedSelectAll) {
                shippingCombinedSelectAll.addEventListener('change', function() {
                    shippingCombinedCheckboxes.forEach(cb => cb.checked = this.checked);
                    updateUI();
                });
            }

            // Event listeners for Services
            serviceCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateUI);
            });
            if (serviceSelectAll) {
                serviceSelectAll.addEventListener('change', function() {
                    serviceCheckboxes.forEach(cb => cb.checked = this.checked);
                    updateUI();
                });
            }

            // Discount and Tax rate changes
            ['discount-storage', 
                'discount-amount-storage', 
                'tax-rate-storage', 
                'discount-shipping', 
                'discount-amount-shipping', 
                'tax-rate-shipping', 
                'discount-combined',
                'discount-amount-combined',
                'tax-rate-combined',
                'discount-services',
                'discount-amount-services',
                'tax-rate-services'
            ].forEach(id => {
                const elem = document.getElementById(id);
                if (elem) {
                    elem.addEventListener('input', updateUI);
                }
            });

            updateUI();
        });
    </script>

@endsection
