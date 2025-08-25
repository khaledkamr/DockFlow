@extends('layouts.admin')

@section('title', 'تفاصيل الإتفاقية')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h2 class="h3 text-primary mb-1">
                    <i class="fas fa-clipboard-list me-2"></i>
                    تفاصيل إتفاقية الإستلام #{{ $policy->id }}
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
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-shield me-2"></i>
                            معلومات السائق والمركبة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <div class="border-bottom pb-3 mb-2">
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
                <div class="card border-0 shadow-sm h-100">
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
                                            <div class="fw-bold text-success fs-5">
                                                {{ $policy->contract->container_storage_price }} ريال لمدة {{ $policy->contract->container_storage_period }} أيام
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center p-3 bg-light rounded">
                                            <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                                            <small class="text-muted d-block">غرامة التأخير</small>
                                            <div class="fw-bold text-danger fs-5">
                                                {{$policy->contract->late_fee}} ريال لليوم الواحد
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center p-3 bg-light rounded">
                                            <i class="fas fa-receipt fa-2x text-primary mb-2"></i>
                                            <small class="text-muted d-block">الضريبة المضافة</small>
                                            <div class="fw-bold text-primary fs-5">15%</div>
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
                                            <div class="fw-bold">{{ $policy->customer->name }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
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
                                    <th class="border-0 fw-bold">تاريخ الدخول</th>
                                    <th class="border-0 fw-bold">مدة تخزين الحاوية</th>
                                    <th class="border-0 fw-bold">سعر التخزين</th>
                                    <th class="border-0 fw-bold">أيام التأخير</th>
                                    <th class="border-0 fw-bold">غرامة تأخير</th>
                                    <th class="border-0 fw-bold">إجمالي المبلغ</th>
                                </trc>
                            </thead>
                            <tbody class="text-center">
                                @foreach($policy->containers as $index => $container)
                                    <tr>
                                        <td>{{ $container->id }}</td>
                                        <td class="fw-bold text-primary">{{ $container->code }}</td>
                                        <td class="fw-bold">{{ $container->containerType->name }}</td>
                                        <td>{{ $container->date }}</td>
                                        <td>{{ $container->period }} يوم</td>
                                        <td>{{ $container->storage_price }} ريال</td>
                                        <td>{{ $container->late_days }} يوم</td>
                                        <td>{{ $container->late_fee }} ريال</td>
                                        <td class="text-success fw-bold">{{ $container->total }} ريال</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد حاويات مرتبطة بهذه الإتفاقية</h5>
                    </div>
                @endif
                <div class="card-footer bg-light">
                    <div class="d-flex gap-3 justify-content-center">
                        <div class="card bg-white shadow-sm" style="min-width: 250px;">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <span class="text-muted fw-bold">خدمة تنزيل الحاويات</span>
                                <span class="fw-bold">{{ $policy->contract->move_container_price * $policy->containers->count() }} ريال</span>
                            </div>
                        </div>
                        <div class="card bg-white shadow-sm" style="min-width: 250px;">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <span class="text-muted fw-bold">المجموع</span>
                                <span class="fw-bold">{{ $policy->containers->sum('total') }} ريال</span>
                            </div>
                        </div>
                        <div class="card bg-white shadow-sm" style="min-width: 250px;">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <span class="text-muted fw-bold">الضريبة (15%)</span>
                                <span class="fw-bold text-dark">{{ $policy->containers->sum('total') * 0.15 }} ريال</span>
                            </div>
                        </div>
                        <div class="card bg-primary bg-opacity-10 shadow-sm" style="min-width: 250px;">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <span class="text-primary fw-bold">الإجمالي النهائي</span>
                                <span class="fw-bold text-primary fs-5">
                                    {{ $policy->containers->sum('total') * 1.15 + $policy->contract->move_container_price * $policy->containers->count() }} ريال
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="d-flex gap-3">
    <button class="btn btn-primary fw-bold">إذن خروج</button>
    <button class="btn btn-primary fw-bold">
        إستخراج فاتورة
        <i class="fa-solid fa-scroll"></i>
    </button>
</div>


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