@extends('layouts.app')

@section('title', 'تفاصيل إشعار النقل')

@section('content')
<h1 class="mb-4">
    <i class="fa-solid fa-truck-fast"></i> تفاصيل إشعار النقل
</h1>

<!-- المعلومات الأساسية -->
<div class="row">
    <!-- معلومات النقل -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fa-solid fa-route"></i> معلومات النقل</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col">
                        <label class="text-muted small">رقم الإشعار</label>
                        <p class="fw-bold mb-0">{{ $transportOrder->code }}</p>
                    </div>
                    <div class="col">
                        <label class="text-muted small">رقم المعاملة</label>
                        <a href="{{ route('transactions.details', $transportOrder->transaction) }}" class="text-decoration-none text-dark fw-bold">
                            {{ $transportOrder->transaction->code }}
                        </a>
                    </div>
                    <div class="col">
                        <label class="text-muted small">التاريخ</label>
                        <p class="fw-bold mb-0">{{ \Carbon\Carbon::parse($transportOrder->date)->format('Y/m/d') }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label class="text-muted small">من</label>
                        <p class="fw-semibold mb-0">{{ $transportOrder->from }}</p>
                    </div>
                    <div class="col">
                        <label class="text-muted small">الى</label>
                        <p class="fw-semibold mb-0">{{ $transportOrder->to }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات السائق والمركبة -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-truck-front-fill"></i> معلومات السائق والمركبة</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col">
                        <label class="text-muted small">الاسم</label>
                        <p class="fw-bold mb-0">{{ $transportOrder->driver->name }}</p>
                    </div>
                    <div class="col">
                        <label class="text-muted small">رقم اللوحة</label>
                        <p class="fw-bold mb-0">{{ $transportOrder->vehicle->plate_number }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label class="text-muted small">الرقم القومي</label>
                        <p class="fw-semibold mb-0">{{ $transportOrder->driver->NID }}</p>
                    </div>
                    <div class="col">
                        <label class="text-muted small">نوع الشاحنة</label>
                        <p class="fw-semibold mb-0">{{ $transportOrder->vehicle->type }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- التكاليف -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-cash-stack"></i> التكاليف</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <label class="text-muted small">تكلفة الديزل</label>
                <p class="fw-bold mb-0">{{ number_format($transportOrder->diesel_cost, 2) }} ريال</p>
            </div>
            <div class="col-md-3">
                <label class="text-muted small">أجرة السائق</label>
                <p class="fw-bold mb-0">{{ number_format($transportOrder->driver_wage, 2) }} ريال</p>
            </div>
            <div class="col-md-3">
                <label class="text-muted small">مصروفات أخرى</label>
                <p class="fw-bold mb-0">{{ number_format($transportOrder->other_expenses, 2) }} ريال</p>
            </div>
            <div class="col-md-3">
                <label class="text-muted small">التكلفة الإجمالية</label>
                <p class="fw-bold text-success mb-0 fs-5">{{ number_format($transportOrder->total_cost, 2) }} ريال</p>
            </div>
        </div>
    </div>
</div>

<!-- الحاويات -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="fa-solid fa-box"></i> الحاويات ({{ count($transportOrder->containers) }})</h5>
    </div>
    <div class="card-body">
        @if(count($transportOrder->containers) > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">رقم الحاوية</th>
                            <th class="text-center">اسم العميل</th>
                            <th class="text-center">النوع</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transportOrder->containers as $index => $container)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center text-primary fw-bold">
                                <a href="{{ route('container.details', $container) }}" class="text-decoration-none">
                                    {{ $container->code }}
                                </a>
                            </td>
                            <td class="text-center fw-bold">
                                <a href="{{ route('users.customer.profile', $container->customer) }}" class="text-decoration-none text-dark">
                                    {{ $container->customer->name }}
                                </a>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-primary">{{ $container->containerType->name }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-success">{{ $container->status }}</span>
                            </td>
                            <td class="text-center">{{ $container->notes ?? '---' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center mb-0">لا توجد حاويات مرتبطة بهذا الإشعار</p>
        @endif
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .card-header {
        border: none;
        padding: 1rem 1.25rem;
    }
    
    .bg-primary {
        background-color: #0d6efd !important;
    }
    
    .bg-info {
        background-color: #0dcaf0 !important;
    }
    
    .text-primary {
        color: #0d6efd !important;
    }
    
    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }
    
    label.text-muted {
        font-weight: 600;
        margin-bottom: 0.25rem;
        display: block;
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
</style>
@endsection