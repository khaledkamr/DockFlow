@extends('layouts.admin')

@section('title', 'تفاصيل العقد #' . $contract->id)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 text-primary">
                <i class="fas fa-file-contract me-2"></i>
                تفاصيل العقد #{{ $contract->id }}
            </h2>
            <div>
                <button class="btn btn-primary me-2">
                    <i class="fas fa-print me-1"></i>
                    طباعة
                </button>
                <button class="btn btn-success">
                    <i class="fas fa-download me-1"></i>
                    تحميل PDF
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    معلومات العقد الأساسية
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="border-end pe-3 text-center">
                            <small class="text-muted">رقم العقد</small>
                            <h6 class="fw-bold text-primary">#{{ $contract->id }}</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end pe-3 text-center">
                            <small class="text-muted">تاريخ البداية</small>
                            <h6 class="fw-bold">{{ \Carbon\Carbon::parse($contract->start_date)->format('Y/m/d') }}</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-end pe-3 text-center">
                            <small class="text-muted">تاريخ الانتهاء</small>
                            <h6 class="fw-bold">{{ \Carbon\Carbon::parse($contract->end_date)->format('Y/m/d') }}</h6>
                        </div>
                    </div>
                    <div class="col-md-3 text-center">
                        <small class="text-muted">مدة العقد</small>
                        <h6 class="fw-bold text-dark">
                            {{ $months }} شهر و {{ $days }} يوم
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-building me-2"></i>
                            الطرف الأول
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label text-muted small">الاسم</label>
                                <div class="fw-bold">{{ $contract->company->name }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">السجل التجاري</label>
                                <div class="fw-bold">{{ $contract->company->CR }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الرقم الضريبي</label>
                                <div class="fw-bold">{{ $contract->company->TIN }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label text-muted small">العنوان</label>
                                <div class="fw-bold">{{ $contract->company->national_address }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">رقم الهاتف</label>
                                <div class="fw-bold">{{ $contract->company->phone }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الإيميل</label>
                                <div class="fw-bold">{{ $contract->company->email }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label text-muted small">ممثل الطرف الأول</label>
                                <div class="fw-bold">{{ $contract->company_representative }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الجنسية</label>
                                <div class="fw-bold">{{ $contract->company_representative_nationality }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الرقم القومي</label>
                                <div class="fw-bold">{{ $contract->company_representative_NID }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-tie me-2"></i>
                            الطرف الثاني
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label text-muted small">الاسم</label>
                                <div class="fw-bold">{{ $contract->customer->name }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">السجل التجاري</label>
                                <div class="fw-bold">{{ $contract->customer->CR }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الرقم الضريبي</label>
                                <div class="fw-bold">{{ $contract->customer->TIN }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label text-muted small">العنوان</label>
                                <div class="fw-bold">{{ $contract->customer->national_address }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">رقم الهاتف</label>
                                <div class="fw-bold">{{ $contract->customer->phone }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الإيميل</label>
                                <div class="fw-bold">{{ $contract->customer->email }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label text-muted small">ممثل الطرف الثاني</label>
                                <div class="fw-bold">{{ $contract->customer_representative }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الجنسية</label>
                                <div class="fw-bold">{{ $contract->customer_representative_nationality }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الرقم القومي</label>
                                <div class="fw-bold">{{ $contract->customer_representative_NID }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    الخدمات والأسعار
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @foreach($contract->services as $index => $service)
                        <div class="col-md-6">
                            <div class="service-card border border-primary rounded p-3 h-100">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="service-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <i class="bi bi-tools"></i>
                                    </div>
                                    <h6 class="mb-0 text-primary">الخدمة #{{ $index + 1 }}</h6>
                                </div>
                                <p class="text-muted mb-2">{{ $service->description }}</p>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">السعر</small>
                                        <div class="fw-bold text-success">
                                            {{ $service->pivot->price != 0 ? $service->pivot->price . ' ريال' : 'مجاناً' }} 
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">المدة</small>
                                        <div class="fw-bold text-dark">{{ $service->pivot->unit . ' ' . $service->pivot->unit_desc }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.service-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.service-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.service-icon {
    font-size: 1.2rem;
}

@media (max-width: 768px) {
    .border-end {
        border-end: none !important;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }
    
    .border-end:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
}
</style>
@endsection