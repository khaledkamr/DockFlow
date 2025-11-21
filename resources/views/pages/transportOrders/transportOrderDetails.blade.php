@extends('layouts.app')

@section('title', 'تفاصيل إشعار النقل')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
    <h1>
        <i class="fa-solid fa-truck-fast d-none d-md-inline"></i>
        <span class="d-none d-md-inline"> تفاصيل إشعار النقل {{ $transportOrder->code }}</span>
        <span class="d-inline d-md-none">إشعار النقل {{ $transportOrder->code }}</span>
    </h1>
    <a href="{{ route('export.transport.order', $transportOrder) }}" target="_blank" class="btn btn-outline-primary">
        <i class="fa-solid fa-print"></i> طباعة الإشعار
    </a>
</div>

<!-- المعلومات الأساسية -->
<div class="row g-3">
    <!-- معلومات النقل -->
    <div class="col-12 col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fa-solid fa-route"></i> معلومات النقل</h5>
            </div>
            <div class="card-body">
                <div class="row g-2 mb-3">
                    <div class="col-6 col-sm-6">
                        <label class="text-muted small d-block">رقم المعاملة</label>
                        <a href="{{ route('transactions.details', $transportOrder->transaction) }}"
                            class="text-decoration-none text-primary fw-bold">
                            {{ $transportOrder->transaction->code }}
                        </a>
                    </div>
                    <div class="col-6 col-sm-6">
                        <label class="text-muted small">التاريخ</label>
                        <p class="fw-bold mb-0">{{ \Carbon\Carbon::parse($transportOrder->date)->format('Y/m/d') }}</p>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6 col-sm-6">
                        <label class="text-muted small">مكان التحميل</label>
                        <p class="fw-semibold mb-0"><i class="fas fa-map-marker-alt text-danger"></i>
                            {{ $transportOrder->from }}</p>
                    </div>
                    <div class="col-6 col-sm-6">
                        <label class="text-muted small">مكان التسليم</label>
                        <p class="fw-semibold mb-0"><i class="fas fa-map-marker-alt text-danger"></i>
                            {{ $transportOrder->to }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات الناقل -->
    <div class="col-12 col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-truck-front-fill"></i> معلومات الناقل</h5>
            </div>
            <div class="card-body">
                <div class="row g-2 mb-3">
                    <div class="col-6 col-sm-6">
                        <label class="text-muted small">نوع الناقل</label>
                        <p class="fw-bold mb-0">{{ $transportOrder->type }}</p>
                    </div>
                    <div class="col-6 col-sm-6">
                        <label
                            class="text-muted small">{{ $transportOrder->driver ? 'اسم السائق' : 'اسم المورد' }}</label>
                        <p class="fw-bold mb-0">
                            {{ $transportOrder->driver->name ?? ($transportOrder->supplier->name ?? '--') }}</p>
                    </div>
                </div>
                <div class="row g-2">
                    @if ($transportOrder->type == 'ناقل داخلي')
                        <div class="col-6 col-sm-6">
                            <label class="text-muted small">الرقم القومي</label>
                            <p class="fw-semibold mb-0">{{ $transportOrder->driver->NID ?? 'N/A' }}</p>
                        </div>
                        <div class="col-6 col-sm-6">
                            <label class="text-muted small">الشاحنة</label>
                            <p class="fw-semibold mb-0">
                                {{ $transportOrder->vehicle->plate_number . ' - ' . $transportOrder->vehicle->type ?? '' }}
                            </p>
                        </div>
                    @elseif($transportOrder->type == 'ناقل خارجي')
                        <div class="col-6 col-sm-6">
                            <label class="text-muted small">اسم السائق</label>
                            <p class="fw-semibold mb-0">{{ $transportOrder->driver_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-6 col-sm-6">
                            <label class="text-muted small">رقم التواصل</label>
                            <p class="fw-semibold mb-0">{{ $transportOrder->vehicle_plate ?? 'N/A' }}</p>
                        </div>
                    @endif
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
        <div class="row g-3">
            @if ($transportOrder->type == 'ناقل داخلي')
                <div class="col-6 col-sm-6 col-md-3">
                    <label class="text-muted small">تكلفة الديزل</label>
                    <p class="fw-bold mb-0">{{ number_format($transportOrder->diesel_cost, 2) }} <i
                            data-lucide="saudi-riyal"></i></p>
                </div>
                <div class="col-6 col-sm-6 col-md-3">
                    <label class="text-muted small">أجرة السائق</label>
                    <p class="fw-bold mb-0">{{ number_format($transportOrder->driver_wage, 2) }} <i
                            data-lucide="saudi-riyal"></i></p>
                </div>
            @elseif($transportOrder->type == 'ناقل خارجي')
                <div class="col-6 col-sm-6 col-md-3">
                    <label class="text-muted small">تكلفة المورد</label>
                    <p class="fw-bold mb-0">{{ number_format($transportOrder->supplier_cost, 2) }} <i
                            data-lucide="saudi-riyal"></i></p>
                </div>
            @endif
            <div class="col-6 col-sm-6 col-md-3">
                <label class="text-muted small">مصروفات أخرى</label>
                <p class="fw-bold mb-0">{{ number_format($transportOrder->other_expenses, 2) }} <i
                        data-lucide="saudi-riyal"></i></p>
            </div>
            <div class="col-6 col-sm-6 col-md-3">
                <label class="text-muted small">سعر العميل</label>
                <p class="fw-bold text-success mb-0 fs-5">{{ number_format($transportOrder->client_cost, 2) }} <i
                        data-lucide="saudi-riyal"></i></p>
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
        @if (count($transportOrder->containers) > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center text-nowrap">#</th>
                            <th class="text-center text-nowrap">رقم الحاوية</th>
                            <th class="text-center text-nowrap">اسم العميل</th>
                            <th class="text-center text-nowrap">النوع</th>
                            <th class="text-center text-nowrap">الحالة</th>
                            <th class="text-center text-nowrap">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transportOrder->containers as $index => $container)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center text-primary fw-bold">
                                    <a href="{{ route('container.details', $container) }}"
                                        class="text-decoration-none">
                                        {{ $container->code }}
                                    </a>
                                </td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('users.customer.profile', $container->customer) }}"
                                        class="text-decoration-none text-dark">
                                        {{ $container->customer->name }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge rounded-pill bg-primary">{{ $container->containerType->name }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-success">{{ $container->status }}</span>
                                </td>
                                <td class="text-center text-nowrap">{{ $container->notes ?? '---' }}</td>
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

<!-- الملاحظات -->
<div class="card shadow-sm mb-5">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="fa-solid fa-sticky-note"></i> الملاحظات</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('transportOrders.notes', $transportOrder) }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="اكتب الملاحظات هنا...">{{ old('notes', $transportOrder->notes) }}</textarea>
            </div>
            <div class="text-start">
                <button type="submit" class="btn btn-primary">
                    حفظ الملاحظات
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }
</style>
@endsection
