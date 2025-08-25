@extends('layouts.admin')

@section('title', 'تفاصيل الإتفاقية #' . $policy->id)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h2 class="h3 text-primary mb-1">
                    <i class="fas fa-clipboard-list me-2"></i>
                    تفاصيل إتفاقية {{ $policy->type == 'تخزين' ? 'التخزين' : 'الإستلام' }} #{{ $policy->id }}
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('contracts.details', $policy->contract_id) }}" class="text-decoration-none">العقد #{{ $policy->contract_id }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">الإتفاقية #{{ $policy->id }}</li>
                    </ol>
                </nav>
            </div>
            <div>
                <button class="btn btn-primary me-2">
                    <i class="fas fa-print me-1"></i>
                    طباعة
                </button>
                <button class="btn btn-secondary me-2">
                    <i class="fas fa-download me-1"></i>
                    تحميل PDF
                </button>
                <button class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>
                    تعديل
                </button>
            </div>
        </div>

        <div class="row">
            <!-- Driver Information -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-shield me-2"></i>
                            معلومات السائق والمركبة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <!-- Driver Info -->
                            <div class="col-12">
                                <div class="border-bottom pb-3 mb-3">
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-user me-2"></i>
                                        بيانات السائق
                                    </h6>
                                    <div class="row">
                                        <div class="col-8">
                                            <label class="form-label text-muted small">اسم السائق</label>
                                            <div class="fw-bold fs-5">{{ $policy->driver_name }}</div>
                                        </div>
                                        <div class="col-4">
                                            <label class="form-label text-muted small">الرقم القومي</label>
                                            <div class="fw-bold">{{ $policy->driver_NID }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Vehicle Info -->
                            <div class="col-12">
                                <h6 class="text-muted mb-2">
                                    <i class="fas fa-truck me-2"></i>
                                    بيانات المركبة
                                </h6>
                                <div class="row">
                                    <div class="col-8">
                                        <label class="form-label text-muted small">نوع المركبة</label>
                                        <div class="fw-bold fs-5">{{ $policy->driver_car }}</div>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label text-muted small">رقم اللوحة</label>
                                        <div class="fw-bold">{{ $policy->car_code }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Policy Financial Information -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            المعلومات المالية والضريبية
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="text-center p-3 bg-light rounded">
                                            <i class="fas fa-warehouse fa-2x text-success mb-2"></i>
                                            <small class="text-muted d-block">سعر التخزين</small>
                                            <div class="fw-bold text-success fs-5">{{ $storage_price == 0 || $storage_price == 'مجاناً' ? 'مجاناً' : $storage_price . ' ريال' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center p-3 bg-light rounded">
                                            <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                                            <small class="text-muted d-block">غرامة التأخير</small>
                                            <div class="fw-bold text-danger fs-5">{{ $late_fee == 0 ? 'لا يوجد' : $late_fee . ' ريال' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center p-3 bg-light rounded">
                                            <i class="fas fa-receipt fa-2x text-warning mb-2"></i>
                                            <small class="text-muted d-block">حالة الضريبة</small>
                                            <div class="fw-bold text-warning fs-6">{{ $tax }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="border-top pt-3">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">تاريخ الإتفاقية</small>
                                            <div class="fw-bold">{{ \Carbon\Carbon::parse($policy->date)->format('Y/m/d') }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">العميل</small>
                                            <div class="fw-bold">#{{ $policy->customer_id }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Containers Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <div class="d-flex justify-content-between align-items-center text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-boxes me-2"></i>
                        الحاويات المشمولة في الإتفاقية
                    </h5>
                    <span class="badge bg-light text-dark">{{ count($policy->containers) }} حاوية</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if(count($policy->containers) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light text-center">
                                <trc>
                                    <th class="border-0 fw-bold">#</th>
                                    <th class="border-0 fw-bold">كود الحاوية</th>
                                    <th class="border-0 fw-bold">نوع الحاوية</th>
                                    <th class="border-0 fw-bold">صاحب الحاوية</th>
                                    <th class="border-0 fw-bold">الحالة</th>
                                    <th class="border-0 fw-bold">الموقع</th>
                                </trc>
                            </thead>
                            <tbody class="text-center">
                                @foreach($policy->containers as $index => $container)
                                <tr>
                                    <td class="align-middle text-center">
                                            {{ $index + 1 }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="fw-bold text-primary">{{ $container->code }}</div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="fw-bold">{{ $container->container_type ? $container->container_type->name : 'null' }}</div>
                                    </td>
                                    <td class="align-middle">
                                        <div>{{ $container->customer->name }}</div>
                                    </td>
                                    <td class="align-middle">
                                        @php
                                            $statusClass = match($container->status) {
                                                'في الإنتظار' => 'bg-warning text-dark',
                                                'مخزن' => 'bg-success',
                                                'مُسلم' => 'bg-info',
                                                'متأخر' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $container->status }}</span>
                                    </td>
                                    <td class="align-middle">
                                        @if($container->location)
                                            <span class="fw-bold">{{ $container->location }}</span>
                                        @else
                                            <span class="text-muted">لم يُحدد بعد</span>
                                        @endif
                                    </td>
                                    
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @else
                    <div class="text-center py-5">
                        <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد حاويات مرتبطة بهذه البوليصة</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@if($policy->type == 'إستلام')
    <div class="d-flex gap-3">
        <button class="btn btn-primary fw-bold">إذن خروج</button>
        <button class="btn btn-primary fw-bold">
            إستخراج فاتورة
            <i class="fa-solid fa-scroll"></i>
        </button>
    </div>
@else
    <button class="btn btn-primary fw-bold">إذن دخول</button>
@endif

<style>
.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-group {
        justify-content: center;
    }
}
</style>
@endsection