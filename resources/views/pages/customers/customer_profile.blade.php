@extends('layouts.app')

@section('title', 'معاينة ملف العميل - ' . $customer['name'])

@section('content')
<style>
    .card-header {
        border-radius: 10px 10px 0 0 !important;
        border: none;
    }

    .bg-outline-primary {
        color: #0d6efd;
        border: 1px solid #0d6efd;
        background-color: transparent;
    }
    
    .table .status-waiting {
        background-color: #ffe590;
        color: #745700;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-available {
        background-color: #d4d7ed;
        color: #151657;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-delivered {
        background-color: #c1eccb;
        color: #155724;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-danger {
        background-color: #f8d7da;
        color: #721c24;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-info {
        background-color: #d1ecf1;
        color: #0c5460;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-purple {
        background-color: #e7d6f7;
        color: #5a2d7a;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    
    /* Table responsive styling */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        position: relative;
    }
    
    .table-responsive::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        width: 30px;
        background: linear-gradient(to left, rgba(0,0,0,0.1), transparent);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .table-responsive.has-scroll::after {
        opacity: 1;
    }
    
    .table {
        min-width: 800px;
    }
    
    @media (max-width: 768px) {
        .table thead th {
            font-size: 12px;
            padding: 10px 6px;
            white-space: nowrap;
        }
        
        .table tbody td {
            font-size: 12px;
            padding: 10px 6px;
            white-space: nowrap;
        }
        
        .table {
            min-width: 900px;
        }
    }
    
    /* Scroll hint */
    .scroll-hint {
        display: none;
        text-align: center;
        color: #6c757d;
        font-size: 13px;
        margin-top: 8px;
        padding: 5px;
    }
    
    @media (max-width: 768px) {
        .scroll-hint {
            display: block;
        }
    }
</style>

<!-- Customer Header -->
<div class="card border-0 rounded-3 shadow-sm mb-3 mb-md-4">
    <div class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <div>
            <h4 class="mb-0 fs-5 fs-md-4">
                <i class="fas fa-user-circle me-2"></i>
                {{ $customer['name'] }}
                ({{ $customer->account->code }})
            </h4>
        </div>
        <div>
            <span class="badge bg-light text-dark fs-6">{{ $customer['type'] }}</span>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-2 g-md-3">
            <div class="col-6 col-sm-6 col-lg-4 col-xl">
                <div class="d-flex align-items-center">
                    <i class="fas fa-receipt text-muted me-2"></i>
                    <div class="flex-grow-1">
                        <small class="text-muted d-block">الرقم الضريبي</small>
                        <div class="fw-bold text-truncate">{{ $customer['vatNumber'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-6 col-lg-4 col-xl">
                <div class="d-flex align-items-center">
                    <i class="fas fa-id-card text-muted me-2"></i>
                    <div class="flex-grow-1">
                        <small class="text-muted d-block">السجل التجاري</small>
                        <div class="fw-bold text-truncate">{{ $customer['CR'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-6 col-lg-4 col-xl">
                <div class="d-flex align-items-center">
                    <i class="fas fa-phone text-muted me-2"></i>
                    <div class="flex-grow-1">
                        <small class="text-muted d-block">رقم الهاتف</small>
                        <div class="fw-bold text-truncate">{{ $customer['phone'] ?? 'غير متوفر' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-6 col-lg-4 col-xl">
                <div class="d-flex align-items-center">
                    <i class="fas fa-envelope text-muted me-2"></i>
                    <div class="flex-grow-1">
                        <small class="text-muted d-block">البريد الإلكتروني</small>
                        <div class="fw-bold text-truncate">{{ $customer['email'] ?? 'غير متوفر' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-6 col-lg-4 col-xl">
                <div class="d-flex align-items-center">
                    <i class="fas fa-map-marker-alt text-muted me-2"></i>
                    <div class="flex-grow-1">
                        <small class="text-muted d-block">العنوان الوطني</small>
                        <div class="fw-bold text-truncate">{{ $customer['national_address'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contract Information -->
@if(isset($customer['contract']))
    <div class="card border-0 rounded-3 shadow-sm mb-3 mb-md-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fs-6 fs-md-5">
                <i class="fas fa-file-contract me-2"></i>
                تفاصيل العقد
            </h5>
            <a href="{{ route('contracts.details', $customer->contract) }}" class="btn btn-sm btn-primary">
                عرض العقد
                <i class="fas fa-eye ms-1"></i>
            </a>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-4">
                    <div class="text-center p-3 border rounded">
                        <i class="fas fa-calendar-check text-success fs-4 mb-2"></i>
                        <div class="small text-muted">تاريخ البداية</div>
                        <div class="fw-bold">{{ Carbon\Carbon::parse($customer->contract->start_date)->format('Y/m/d') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="text-center p-3 border rounded">
                        <i class="fas fa-calendar-times text-danger fs-4 mb-2"></i>
                        <div class="small text-muted">تاريخ الانتهاء</div>
                        <div class="fw-bold">{{ Carbon\Carbon::parse($customer->contract->end_date)->format('Y/m/d') }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-4 d-none d-md-block">
                    <div class="text-center p-3 border rounded">
                        <i class="fas fa-hourglass-half text-warning fs-4 mb-2"></i>
                        <div class="small text-muted">مدة السماح للدفع</div>
                        <div class="fw-bold">
                            {{ $customer->contract->payment_grace_period . ' ' . $customer->contract->payment_grace_period_unit }}
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="text-dark mb-3 fs-6">
                <i class="fas fa-cogs me-2"></i>
                الخدمات المتعاقد عليها
            </h6>
            <div class="row g-3">
                @foreach($customer->contract->services as $service)
                    <div class="col-12 col-lg-6">
                        <div class="card bg-light border border-primary">
                            <div class="card-body">
                                <h6 class="card-title text-primary fw-bold fs-6">{{ $service->description }}</h6>
                                <div class="d-flex flex-row justify-content-between gap-2">
                                    <div class="d-flex gap-2">
                                        <span>السعر:</span>
                                        <strong>{{ $service->pivot->price }} <i data-lucide="saudi-riyal"></i></strong>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <span>المدة/الكمية:</span>
                                        <strong>{{ $service->pivot->unit .' '. $service->pivot->unit_desc }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<!-- Containers -->
<div class="card border-0 rounded-3 shadow-sm mb-3 mb-md-4">
    <div class="card-header bg-dark text-white d-flex flex-row justify-content-between align-items-start align-items-md-center gap-2">
        <h5 class="mb-0 fs-6 fs-md-5">
            <i class="fas fa-shipping-fast me-2"></i>
            الحاويات ({{ count($customer['containers']) }})
        </h5>
        <div>
            <span class="badge bg-primary">
                في الساحة: {{ collect($customer['containers'])->where('status', 'في الساحة')->count() }}
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" id="containersTable">
            <table class="table table-hover mb-0">
                <thead class="table-primary text-center">
                    <tr>
                        <th class="text-center fw-bold">#</th>
                        <th class="text-center fw-bold">رقم الحاوية</th>
                        <th class="text-center fw-bold">نوع الحاوية</th>
                        <th class="text-center fw-bold">الحالة</th>
                        <th class="text-center fw-bold">الموقع</th>
                        <th class="text-center fw-bold">تاريخ الدخول</th>
                        <th class="text-center fw-bold">تاريخ الخروج</th>
                        <th class="text-center fw-bold">ملاحظات</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach($customer['containers'] as $index => $container)
                        <tr>
                            <td class="fw-bold text-primary">{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $container->code }}</td>
                            <td>
                                <span class="badge bg-outline-primary">
                                    {{ $container->containerType->name }}
                                </span>
                            </td>
                            <td>
                                @if($container->status == 'في الساحة')
                                    <div class="status-available">{{ $container->status }}</div>
                                @elseif($container->status == 'تم التسليم')
                                    <div class="status-delivered">{{ $container->status }} <i class="fa-solid fa-check"></i></div>
                                @elseif($container->status == 'متأخر')
                                    <div class="status-danger">{{ $container->status }}</div>
                                @elseif($container->status == 'خدمات')
                                    <div class="status-waiting">{{ $container->status }}</div>
                                @elseif($container->status == 'في الميناء')
                                    <div class="status-info">{{ $container->status }}</div>
                                @elseif($container->status == 'قيد النقل')
                                    <div class="status-purple">{{ $container->status }}</div>
                                @endif
                            </td>
                            <td class="{{ $container->location ? 'fw-bold' : 'text-muted' }}">{{ $container->location ?? 'غير محدد' }}</td>
                            <td>{{ $container->date ? Carbon\Carbon::parse($container->date)->format('Y/m/d') : '-' }}</td>
                            <td>{{ $container->exit_date ? Carbon\Carbon::parse($container->exit_date)->format('Y/m/d') : '-' }}</td>
                            <td>{{ $container->noted }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="scroll-hint">
            <i class="fa-solid fa-arrows-left-right me-1"></i>
            اسحب الجدول لليمين أو اليسار لرؤية المزيد
        </div>
    </div>
</div>

<!-- Invoices -->
<div class="card border-0 rounded-3 shadow-sm mb-3 mb-md-4">
    <div class="card-header bg-dark text-white d-flex flex-row justify-content-between align-items-start align-items-md-center gap-2">
        <h5 class="mb-0 fs-6 fs-md-5">
            <i class="fas fa-file-invoice-dollar me-2"></i>
            الفواتير ({{ count($customer->invoices) }})
        </h5>
        <div class="d-flex gap-2 flex-wrap">
            <span class="badge bg-success">
                مدفوع: {{ collect($customer->invoices)->where('isPaid', 'تم الدفع')->count() }}
            </span>
            <span class="badge bg-danger">
                غير مدفوع: {{ collect($customer->invoices)->where('isPaid', 'لم يتم الدفع')->count() }}
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" id="invoicesTable">
            <table class="table table-hover mb-0">
                <thead class="table-primary text-center">
                    <tr>
                        <th class="text-center fw-bold">#</th>
                        <th class="text-center fw-bold">رقم الفاتورة</th>
                        <th class="text-center fw-bold">نوع الفاتورة</th>
                        <th class="text-center fw-bold">المبلغ</th>
                        <th class="text-center fw-bold">طريقة الدفع</th>
                        <th class="text-center fw-bold">الحالة</th>
                        <th class="text-center fw-bold">التاريخ</th>
                        <th class="text-center fw-bold">تم بواسطة</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach($customer->invoices as $invoice)
                    <tr>
                        <td class="fw-bold text-primary">{{ $loop->iteration }}</td>
                        <td class="fw-bold text-primary">
                            @if ($invoice->type == 'خدمات')
                                <a href="{{ route('invoices.services.details', $invoice) }}"
                                    class="text-decoration-none">
                                    {{ $invoice->code }}
                                </a>
                            @elseif($invoice->type == 'تخزين')
                                <a href="{{ route('invoices.details', $invoice) }}" class="text-decoration-none">
                                    {{ $invoice->code }}
                                </a>
                            @elseif($invoice->type == 'تخليص')
                                <a href="{{ route('invoices.clearance.details', $invoice) }}"
                                    class="text-decoration-none">
                                    {{ $invoice->code }}
                                </a>
                            @elseif($invoice->type == 'شحن')
                                <a href="{{ route('invoices.shipping.details', $invoice) }}"
                                    class="text-decoration-none">
                                    {{ $invoice->code }}
                                </a>
                            @endif
                        </td>
                        <td>{{ $invoice->type }}</td>
                        <td class="fw-bold">{{ $invoice->total_amount }} <i data-lucide="saudi-riyal"></i></td>
                        <td>{{ $invoice->payment_method }}</td>
                        <td>
                            @if($invoice->isPaid === 'تم الدفع')
                                <span class="badge status-available">{{ $invoice->isPaid }}</span>
                            @else
                                <span class="badge status-danger">{{ $invoice->isPaid }}</span>
                            @endif
                        </td>
                        <td>{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') }}</td>
                        <td>{{ $invoice->made_by->name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="scroll-hint">
            <i class="fa-solid fa-arrows-left-right me-1"></i>
            اسحب الجدول لليمين أو اليسار لرؤية المزيد
        </div>
        <div class="card-footer bg-light">
            <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                <div class="card text-center px-3 px-md-5 py-2 border-success flex-grow-1 flex-md-grow-0">
                    <small class="text-success fw-bold">إجمالي المدفوع</small>
                    <div class="fw-bold text-success">
                        {{ collect($customer->invoices)->where('isPaid', 'تم الدفع')->sum('total_amount') }} 
                        <i data-lucide="saudi-riyal"></i>
                    </div>
                </div>
                <div class="card text-center px-3 px-md-5 py-2 border-danger flex-grow-1 flex-md-grow-0">
                    <small class="text-danger fw-bold">إجمالي المستحق</small>
                    <div class="fw-bold text-danger">
                        {{ collect($customer->invoices)->where('isPaid', 'لم يتم الدفع')->sum('total_amount') }} 
                        <i data-lucide="saudi-riyal"></i>
                    </div>
                </div>
                <div class="card text-center px-3 px-md-5 py-2 border-primary flex-grow-1 flex-md-grow-0">
                    <small class="text-primary fw-bold">إجمالي الفواتير</small>
                    <div class="fw-bold text-primary">
                        {{ collect($customer->invoices)->sum('total_amount') }} 
                        <i data-lucide="saudi-riyal"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
        checkTableScroll('containersTable');
        checkTableScroll('invoicesTable');
        
        // Re-check on window resize
        window.addEventListener('resize', function() {
            checkTableScroll('containersTable');
            checkTableScroll('invoicesTable');
        });
        
        // Hide scroll hints after first scroll
        const scrollHints = document.querySelectorAll('.scroll-hint');
        const tables = document.querySelectorAll('.table-responsive');
        
        tables.forEach((table, index) => {
            if (scrollHints[index]) {
                table.addEventListener('scroll', function() {
                    scrollHints[index].style.display = 'none';
                }, { once: true });
            }
        });
    });
</script>
@endsection