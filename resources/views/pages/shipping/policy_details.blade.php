@extends('layouts.app')

@section('title', 'بوليصة شحن')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>
        <i class="fa-solid fa-truck-fast"></i> تفاصيل بوليصة الشحن {{ $policy->code }}
    </h1>
    <a href="{{ route('export.shipping.policy', $policy->id) }}" target="_blank" class="btn btn-outline-primary">
        <i class="fa-solid fa-print"></i> طباعة البوليصة
    </a>
</div>

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
                        <label class="text-muted small">مكان التحميل</label>
                        <p class="fw-semibold mb-0"><i class="fas fa-map-marker-alt text-danger"></i> {{ $policy->from }}</p>
                    </div>
                    <div class="col">
                        <label class="text-muted small">مكان التسليم</label>
                        <p class="fw-semibold mb-0"><i class="fas fa-map-marker-alt text-danger"></i> {{ $policy->to }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label class="text-muted small">التاريخ</label>
                        <p class="fw-bold mb-0">{{ \Carbon\Carbon::parse($policy->date)->format('Y/m/d') }}</p>
                    </div>
                    <div class="col">
                        <label class="text-muted small">تم الاستلام؟</label>
                        @if($policy->is_received)
                            <span class="badge status-delivered">
                                <i class="fa-solid fa-check-circle"></i> تم الاستلام
                            </span>
                        @else
                            <span class="badge status-waiting">
                                <i class="fa-solid fa-clock"></i> في الانتظار
                            </span>
                        @endif
                        <form method="POST" action="{{ route('shipping.policies.toggle', $policy) }}" class="d-inline mt-2">
                            @csrf
                            @method('PATCH')
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isReceivedToggle"
                                    {{ $policy->is_received ? 'checked' : '' }}
                                    onchange="this.form.submit()">
                                <label class="form-check-label small" for="isReceivedToggle">
                                    تغيير الحالة
                                </label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات الناقل -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-truck-front-fill"></i> معلومات الناقل</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col">
                        <label class="text-muted small">نوع الناقل</label>
                        <p class="fw-bold mb-0">{{ $policy->type }}</p>
                    </div>
                    <div class="col">
                        <label class="text-muted small">{{ $policy->driver ? 'اسم السائق' : 'اسم المورد' }}</label>
                        <p class="fw-bold mb-0">{{ $policy->driver->name ?? $policy->supplier->name ?? '--' }}</p>
                    </div>
                </div>
                <div class="row">
                    @if($policy->type == 'ناقل داخلي')
                        <div class="col">
                            <label class="text-muted small">الرقم القومي</label>
                            <p class="fw-semibold mb-0">{{ $policy->driver->NID ?? 'N/A' }}</p>
                        </div>
                        <div class="col">
                            <label class="text-muted small">الشاحنة</label>
                            <p class="fw-semibold mb-0">{{ $policy->vehicle->plate_number . ' - ' . $policy->vehicle->type ?? '' }}</p>
                        </div>
                    @elseif($policy->type == 'ناقل خارجي')
                        <div class="col">
                            <label class="text-muted small">اسم السائق</label>
                            <p class="fw-semibold mb-0">{{ $policy->driver_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col">
                            <label class="text-muted small">رقم التواصل</label>
                            <p class="fw-semibold mb-0">{{ $policy->vehicle_plate ?? 'N/A' }}</p>
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
        <div class="row">
            @if($policy->type == 'ناقل داخلي')
                <div class="col-md-3">
                    <label class="text-muted small">تكلفة الديزل</label>
                    <p class="fw-bold mb-0">{{ number_format($policy->diesel_cost, 2) }} <i data-lucide="saudi-riyal"></i></p>
                </div>
                <div class="col-md-3">
                    <label class="text-muted small">أجرة السائق</label>
                    <p class="fw-bold mb-0">{{ number_format($policy->driver_wage, 2) }} <i data-lucide="saudi-riyal"></i></p>
                </div>
            @elseif($policy->type == 'ناقل خارجي')
                <div class="col-md-3">
                    <label class="text-muted small">تكلفة المورد</label>
                    <p class="fw-bold mb-0">{{ number_format($policy->supplier_cost, 2) }} <i data-lucide="saudi-riyal"></i></p>
                </div>
            @endif
            <div class="col-md-3">
                <label class="text-muted small">مصروفات أخرى</label>
                <p class="fw-bold mb-0">{{ number_format($policy->other_expenses, 2) }} <i data-lucide="saudi-riyal"></i></p>
            </div>
            <div class="col-md-3">
                <label class="text-muted small">سعر العميل</label>
                <p class="fw-bold text-success mb-0 fs-5">{{ number_format($policy->client_cost, 2) }} <i data-lucide="saudi-riyal"></i></p>
            </div>
        </div>
    </div>
</div>

<!-- الحاويات -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="fa-solid fa-box"></i> البضائع</h5>
    </div>
    <div class="card-body">
        @if($policy->goods && count($policy->goods) > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center">البيان</th>
                            <th class="text-center">الكمية</th>
                            <th class="text-center">الوزن</th>
                            <th class="text-center">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($policy->goods as $good)
                            <tr>
                                <td class="text-center fw-bold">{{ $good->description }}</td>
                                <td class="text-center">{{ $good->quantity ?? '---' }}</td>
                                <td class="text-center">{{ $good->weight ?? '---' }}</td>
                                <td class="text-center text-muted">{{ $good->notes ?? '---' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center text-muted py-4">
                <i class="fa-solid fa-box-open fa-3x mb-3"></i>
                <p>لا توجد بضائع مرتبطة بهذه البوليصة</p>
            </div>
        @endif
    </div>
</div>

<!-- الملاحظات -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="fa-solid fa-sticky-note"></i> الملاحظات</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('shipping.policies.notes', $policy) }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <textarea class="form-control" id="notes" name="notes" rows="3" 
                    placeholder="اكتب الملاحظات هنا...">{{ old('notes', $policy->notes) }}</textarea>
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