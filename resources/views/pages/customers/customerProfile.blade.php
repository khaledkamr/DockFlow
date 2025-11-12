@extends('layouts.app')

@section('title', 'معاينة ملف العميل - ' . $customer['name'])

@section('content')
<!-- Customer Header -->
<div class="card border-0 rounded-3 shadow-sm mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0">
                <i class="fas fa-user-circle me-2"></i>
                {{ $customer['name'] }}
            </h4>
        </div>
        <div class="text-end">
            <span class="badge bg-light text-dark fs-6">{{ $customer['type'] }}</span>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col">
                <div class="d-flex align-items-center">
                    <i class="fas fa-receipt text-muted me-2"></i>
                    <div>
                        <small class="text-muted">الرقم الضريبي</small>
                        <div class="fw-bold">{{ $customer['vatNumber'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="d-flex align-items-center">
                    <i class="fas fa-id-card text-muted me-2"></i>
                    <div>
                        <small class="text-muted">السجل التجاري</small>
                        <div class="fw-bold">{{ $customer['CR'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="d-flex align-items-center">
                    <i class="fas fa-phone text-muted me-2"></i>
                    <div>
                        <small class="text-muted">رقم الهاتف</small>
                        <div class="fw-bold">{{ $customer['phone'] ?? 'غير متوفر' }}</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="d-flex align-items-center">
                    <i class="fas fa-envelope text-muted me-2"></i>
                    <div>
                        <small class="text-muted">البريد الإلكتروني</small>
                        <div class="fw-bold">{{ $customer['email'] ?? 'غير متوفر' }}</div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="d-flex align-items-center">
                    <i class="fas fa-map-marker-alt text-muted me-2"></i>
                    <div>
                        <small class="text-muted">العنوان الوطني</small>
                        <div class="fw-bold">{{ $customer['national_address'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contract Information -->
@if(isset($customer['contract']))
    <div class="card border-0 rounded-3 shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">
                <i class="fas fa-file-contract me-2"></i>
                تفاصيل العقد
            </h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded">
                        <i class="fas fa-calendar-check text-success fs-4 mb-2"></i>
                        <div class="small text-muted">تاريخ البداية</div>
                        <div class="fw-bold">{{ Carbon\Carbon::parse($customer->contract->start_date)->format('Y/m/d') }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded">
                        <i class="fas fa-calendar-times text-danger fs-4 mb-2"></i>
                        <div class="small text-muted">تاريخ الانتهاء</div>
                        <div class="fw-bold">{{ Carbon\Carbon::parse($customer->contract->end_date)->format('Y/m/d') }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 border rounded">
                        <i class="fas fa-hourglass-half text-warning fs-4 mb-2"></i>
                        <div class="small text-muted">مدة العقد</div>
                        <div class="fw-bold">
                            {{ \Carbon\Carbon::parse($customer->contract->start_date)->diffInDays(\Carbon\Carbon::parse($customer->contract->end_date)) }} يوم
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="text-dark mb-3">
                <i class="fas fa-cogs me-2"></i>
                الخدمات المتعاقد عليها
            </h6>
            <div class="row g-3">
                @foreach($customer->contract->services as $service)
                    <div class="col-md-6">
                        <div class="card bg-light border border-primary">
                            <div class="card-body">
                                <h6 class="card-title text-primary">{{ $service->description }}</h6>
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex gap-3">
                                        <span>السعر:</span>
                                        <strong>{{ $service->pivot->price }} <i data-lucide="saudi-riyal"></i></strong>
                                    </div>
                                    <div class="d-flex gap-3">
                                        <span>المدة:</span>
                                        <strong>{{ $service->pivot->unit .' ', $service->pivot->desc_unit }} أيام</strong>
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
<div class="card border-0 rounded-3 shadow-sm mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-shipping-fast me-2"></i>
            الحاويات ({{ count($customer['containers']) }})
        </h5>
        <div>
            <span class="badge bg-primary me-1">
                في الساحة: {{ collect($customer['containers'])->where('status', 'في الساحة')->count() }}
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
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
                        <td><span class="badge bg-outline-primary">{{ $container->containerType->name }}</span></td>
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
    </div>
</div>

<!-- Invoices -->
<div class="card border-0 rounded-3 shadow-sm mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-file-invoice-dollar me-2"></i>
            الفواتير ({{ count($customer->invoices) }})
        </h5>
        <div>
            <span class="badge bg-success me-1">
                مدفوع: {{ collect($customer->invoices)->where('isPaid', 'تم الدفع')->count() }}
            </span>
            <span class="badge bg-danger">
                غير مدفوع: {{ collect($customer->invoices)->where('isPaid', 'لم يتم الدفع')->count() }}
            </span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-primary text-center">
                    <tr>
                        <th class="text-center fw-bold">#</th>
                        <th class="text-center fw-bold">رقم الفاتورة</th>
                        <th class="text-center fw-bold">المبلغ</th>
                        <th class="text-center fw-bold">طريقة الدفع</th>
                        <th class="text-center fw-bold">الحالة</th>
                        <th class="text-center fw-bold">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach($customer->invoices as $invoice)
                    <tr>
                        <td class="fw-bold text-primary">{{ $loop->iteration }}</td>
                        <td class="fw-bold text-primary">
                            <a href="{{ route('invoices.details', $invoice) }}" class="text-decoration-none">
                                {{ $invoice->code }}
                            </a>
                        </td>
                        <td class="fw-bold">{{ $invoice->total_amount }} <i data-lucide="saudi-riyal"></i></td>
                        <td>{{ $invoice->payment_method }}</td>
                        <td>
                            @if($invoice->isPaid === 'تم الدفع')
                                <span class="status-available">{{ $invoice->isPaid }}</span>
                            @else
                                <span class="status-danger">{{ $invoice->isPaid }}</span>
                            @endif
                        </td>
                        <td>{{ Carbon\Carbon::parse($invoice->date)->format('Y/m/d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-center gap-3">
                <div class="card text-center px-5 py-2 border-success">
                    <small class="text-success fw-bold">إجمالي المدفوع</small>
                    <div class="fw-bold text-success">
                        {{ collect($customer->invoices)->where('isPaid', 'تم الدفع')->sum('total_amount') }} 
                        <i data-lucide="saudi-riyal"></i>
                    </div>
                </div>
                <div class="card text-center px-5 py-2 border-danger">
                    <small class="text-danger fw-bold">إجمالي المستحق</small>
                    <div class="fw-bold text-danger">
                        {{ collect($customer->invoices)->where('isPaid', 'لم يتم الدفع')->sum('total_amount') }} 
                        <i data-lucide="saudi-riyal"></i>
                    </div>
                </div>
                <div class="card text-center px-5 py-2 border-primary">
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
</style>
@endsection